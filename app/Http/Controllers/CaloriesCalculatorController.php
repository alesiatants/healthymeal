<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Recipes;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
/**
 * Контроллер для расчета калорий гна каждый прием пищи на неделю, подбор рецептов на день
 */
class CaloriesCalculatorController extends Controller
{
    const COEFICIENTS = [
        'male' => [
            [0.063, 2.896], // 18-30
            [0.048, 3.653], // 31-60
            [0.049, 2.459], // 61+
        ],
        'female' => [
            [0.062, 2.036], // 18-30
            [0.034, 3.538], // 31-60
            [0.038, 2.755], // 61+
        ],
    ];
    const ACTIVITY = [
        'Умственный' => 1.4,
        'Лёгкий' => 1.6,
        'Средний' => 1.9,
        'Тяжёлый' => 2.2,
        'Сверхтяжёлый' => 2.5,
    ];
    const DAILYFACTORS = [
        'Понедельник' => 1.0, 'Вторник' => 0.9, 'Среда' => 0.85,
        'Четверг' => 1.1, 'Пятница' => 1.0, 'Суббота' => 1.3,
        'Воскресенье' => 1.15
    ];
    const RUSSIANDAYS = [
        'monday' => 'Понедельник',
        'tuesday' => 'Вторник',
        'wednesday' => 'Среда',
        'thursday' => 'Четверг',
        'friday' => 'Пятница',
        'saturday' => 'Суббота',
        'sunday' => 'Воскресенье'
    ];

    /**
     * Выгрузка интерфейса расчета плана питания
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show()
    {
        $user = Auth::user();
        $currentPlan = $user->currentPlan();
        return view('calculator.calculator', [
                    'user' => $user,
                    'currentPlan' => $currentPlan
                    ]);
    }
    
   /**
     * Расчет недельного и дневного плана питания
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function calculate(Request $request)
    {
        Log::info("Расчет плана");
        $validated = $request->validate([
            'birthday' => 'required|date',
            'weight' => 'required|numeric|min:35|max:200',
            'gender' => 'required|in:male,female',
            'goal' => ['required', Rule::in(['Набрать вес', 'Сбросить вес', 'Поддержать вес'])],
            'activity_level' => ['required', Rule::in(['Умственный', 'Лёгкий', 'Средний', 'Тяжёлый', 'Сверхтяжёлый'])],
            'meals_per_day' => ['required', Rule::in([3, 4, 5])],
        ]);
        Log::debug("Данные запроса для расачета плана питания:\n" . json_encode($validated, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        $birthday = $request->input('birthday');
        $age = Carbon::parse($birthday)->age;
        $weight = $request->input('weight');
        $gender = $request->input('gender');
        $activity_level = $request->input('activity_level');
        $goal = $request->input('goal');
        $mealsPerDay = $request->input('meals_per_day');
        $calories = $this->calculateCalories($weight, $gender, $activity_level, $goal, $age);
        // Зигзагообразное распределение по неделе
        $weeklyPlan = $this->calculateWeeklyPlan($calories, $mealsPerDay)["weekly_plan"];
        $todayPlan = $this->calculateWeeklyPlan($calories, $mealsPerDay)["today_plan"];
        $request->flash();
        session()->flash('results', [
            'weeklyPlan' => $weeklyPlan,
            'chartData' => json_encode($weeklyPlan),
            'todayPlan' => $todayPlan
        ]);
        Log::debug("Рассчитанные данные:\nНедельный план:\n" . json_encode($weeklyPlan, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\nПлан на текущий день:\n" .json_encode($todayPlan, JSON_PRETTY_PRINT| JSON_UNESCAPED_UNICODE));
        
        // Возвращаем результат в представление
        return redirect()->route('calculator.show');
    }

    private function calculateCalories($weight, $gender, $activityLevel, $goal, $age)
    {   
		$ageGroup = $age < 18 ? 0 : ($age <= 30 ? 0 : ($age <= 60 ? 1 : 2));
		$calories = (self::COEFICIENTS[$gender][$ageGroup][0] * $weight + self::COEFICIENTS[$gender][$ageGroup][1]) * 240 * 
		self::ACTIVITY[$activityLevel];
        switch ($goal) {
			case 'Набрать вес':return  round($calories * 1.1);
			case 'Сбросить вес':return round($calories * 0.80);
			default:return round($calories);} }
    private function distributeCalories($calories, $mealsPerDay)
    {
        $distribution = [];
        switch ($mealsPerDay) {
            case 3:
                $distribution = [
                    'завтрак' => round($calories * 0.35),
                    'обед' => round($calories * 0.40),
                    'ужин' => round($calories * 0.25),];
                break;
            case 4:
                $distribution = [
                    'завтрак' => round($calories * 0.30),
                    'перекус' => round($calories * 0.20),
                    'обед' => round($calories * 0.35),
                    'ужин' => round($calories * 0.15),];
                break;
            case 5:
                $distribution = [
                    'завтрак' => round($calories * 0.25),
                    'перекус' => round($calories * 0.15),
                    'обед' => round($calories * 0.30),
                    'полдник' => round($calories * 0.15),
                    'ужин' => round($calories * 0.15),];
                break;  }
        return $distribution;
    }
    private function calculateWeeklyPlan($baseCalories, $mealsPerDay)
    {
        $weeklyPlan = [];
        $currentDay = strtolower(date('l')); 
        $currentDayRussian = self::RUSSIANDAYS[$currentDay] ?? $currentDay;
    
        foreach (self::DAILYFACTORS as $day => $factor) {
            $dayCalories = round($baseCalories * $factor);
            $dailyMeals = $this->distributeCalories($dayCalories, $mealsPerDay);
            $weeklyPlan[$day] = [
                'total' => $dayCalories,
                'meals' =>  $dailyMeals ];
            if (strtolower($day) === $currentDayRussian) {
                $todayPlan = array_map(function($mealType, $calories) {
                    return $this->findPriemProposition($calories, $mealType);
                }, array_keys($dailyMeals), $dailyMeals);   
                $todayPlan = array_combine(array_keys($dailyMeals), $todayPlan); }}
        return [
            'weekly_plan' => $weeklyPlan,
            'today_plan' => $todayPlan ?? []];}
    private function findPriemProposition($caloriesPriem, $typePriem) 
    {
        switch ($typePriem) {
			case 'завтрак':
				return Recipes::typeZavtrakFilter($caloriesPriem);
			case 'перекус':
            case 'полдник':
				return Recipes::typePerekusFilter($caloriesPriem);
            case 'обед':
                return Recipes::typeObedFilter($caloriesPriem);;
            case 'ужин':
                return Recipes::typeUginFilter($caloriesPriem);
		}
    }
}
