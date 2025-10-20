 <!-- Logo Section (Centered) -->
 <div class="flex justify-center flex-1">
     <div class="flex-shrink-0 flex items-center">
         <a href="#" class="flex items-center text-lg font-semibold text-gray-800">
             <i class="bi bi-bus-front mr-2"></i>HIGHLINK ISGC
         </a>
     </div>
 </div>

 <!-- User Menu (Right, visible in all modes) -->
 <div class="flex items-center">
     <div class="relative flex">
         <select
             class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
             onchange="window.location.href = '{{ route('set.currency', ['currency' => ':currency']) }}'.replace(':currency', this.value)">
             <option value="Tsh" {{ session('currency') == 'Tsh' ? 'selected' : '' }}>Tsh
             </option>
             <option value="Usd" {{ session('currency') == 'Usd' ? 'selected' : '' }}>Usd
             </option>
         </select>
         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
             <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                 <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
             </svg>
         </div>

         <select
             class="block appearance-none w-full bg-white border border-gray-300 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
             onchange="window.location.href = '{{ route('set.locale', ['lang' => '']) }}' + this.value">
             <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English
             </option>
             <option value="sw" {{ app()->getLocale() == 'sw' ? 'selected' : '' }}>Kiswahili
             </option>
         </select>
         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
             <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                 <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
             </svg>
         </div>
     </div>
     <button class="flex items-center text-gray-700 hover:text-gray-900 focus:outline-none" id="userMenuButton"
         aria-expanded="false" aria-haspopup="true">
         <i class="bi bi-person-circle mr-1"></i>
         <span>{{ auth()->user()->name }}</span>
     </button>

 </div>
