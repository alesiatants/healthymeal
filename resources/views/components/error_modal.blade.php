@section('errormodal')
@props(['value'])

<div id="error-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Ошибка</h3>
                <div class="mt-2">
                    <p id="modal-error-text" class="text-sm text-gray-500"></p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button id="close-modal-btn" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#db2626] text-base font-medium text-white hover:bg-[#c52222] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#db2626] sm:ml-3 sm:w-auto sm:text-sm">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
  </div>
  @endsection