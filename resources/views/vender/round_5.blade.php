@extends('vender.app')

@section('content')
    <section class="bg-gradient-to-b from-gray-200 to-gray-600 py-8 px-4 sm:px-6 lg:px-8 rounded-3xl">
        <div class="max-w-9/12 mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
                <!-- Passenger Details Column -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-xl font-bold text-gray-900">{{ __('customer/busroot.complete_your_booking') }}
                            </h5>
                            <span class="text-sm text-gray-600">
                                {{ $info['from'] ?? __('customer/busroot.na') }} {{ __('customer/busroot.to') }}
                                {{ $info['to'] ?? __('customer/busroot.na') }} |
                                {{ $info['travel_date'] ?? __('customer/busroot.na') }}
                                {{ $time['start'] ?? __('customer/busroot.na') }}
                            </span>
                        </div>

                        <form action="{{ route('round.trip.payment.pay') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Trip and Passenger Details -->
                                <div class="lg:col-span-2">
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h6 class="text-lg font-bold text-gray-900">
                                                    {{ $car->busname->name ?? __('customer/busroot.na') }}</h6>
                                                @php
                                                    $mode = [
                                                        '10' => 'LUXURY',
                                                        '20' => 'UPPER-SEMILUXURY',
                                                        '30' => 'LOWER-SEMILUXURY',
                                                        '40' => 'ORDINARY',
                                                    ];
                                                    $busType =
                                                        isset($car->bus_type) && array_key_exists($car->bus_type, $mode)
                                                            ? $mode[$car->bus_type]
                                                            : __('customer/busroot.na');
                                                @endphp
                                                <p class="text-sm text-gray-500">
                                                    {{ $busType }} | <span
                                                        class="text-red-500">{{ __('customer/busroot.via') }}
                                                        {{ $car->route->via->name ?? __('customer/busroot.na') }}</span>
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <h6 class="text-lg font-bold text-gray-900">
                                                    {{ __('customer/busroot.seat_no') }}
                                                    <span>{{ $seats ?? __('customer/busroot.na') }}</span></h6>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-3">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $info['travel_date'] ?? __('customer/busroot.na') }}</p>
                                                <p class="font-medium text-gray-800">
                                                    {{ $info['from'] ?? __('customer/busroot.na') }}
                                                    {{ $time['start'] ?? __('customer/busroot.na') }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-sm text-blue-600">{{ __('customer/busroot.via') }}
                                                    {{ $car->route->via->name ?? __('customer/busroot.na') }}</p>
                                            </div>
                                            <div class="text-right sm:text-left">
                                                <p class="text-sm text-gray-500">
                                                    {{ $info['travel_date'] ?? __('customer/busroot.na') }}</p>
                                                <p class="font-medium text-gray-800">
                                                    {{ $info['to'] ?? __('customer/busroot.na') }}
                                                    {{ $time['end'] ?? __('customer/busroot.na') }}</p>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <h6 class="font-bold text-gray-900">
                                                {{ __('customer/busroot.passenger_details') }}</h6>
                                            <input type="hidden" value="L-10-4-K1" id="seatId_0_0">

                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-3">
                                                <div>
                                                    <label for="full_name_0_0"
                                                        class="block text-sm font-semibold text-gray-800">{{ __('customer/busroot.full_name') }}<span
                                                            class="text-red-500">*</span></label>
                                                    <input type="text" name="customer" id="full_name_0_0"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 capitalize"
                                                        maxlength="30" required>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-semibold text-gray-800">{{ __('customer/busroot.gender') }}</label>
                                                    <select name="gender"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        required>
                                                        <option value="Male">{{ __('customer/busroot.male') }}</option>
                                                        <option value="Female">{{ __('customer/busroot.female') }}</option>
                                                    </select>
                                                </div>
                                                <!-- Age -->
                                                <div>
                                                    <label for="age_0_0"
                                                        class="block text-sm font-semibold text-gray-800">{{ __('customer/busroot.age') }}<span
                                                            class="text-red-500">*</span></label>
                                                    <input type="number" name="age" id="age_0_0"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        min="0" max="120" required>
                                                </div>
                                                <!-- Infant Child Checkbox -->
                                                <div class="flex items-center mt-4">
                                                    <input type="checkbox" id="infant_child_0_0" name="infant_child"
                                                        value="1"
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                    <label for="infant_child_0_0"
                                                        class="ml-2 block text-sm font-medium text-gray-700">{{ __('customer/busroot.user_has_infant_child') }}</label>
                                                </div>
                                                <!-- Excess Luggage Checkbox -->
                                                <div class="flex items-center mt-4">
                                                    <input type="checkbox" id="excess_luggage_roundtrip" name="excess_luggage"
                                                        value="1"
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                        onchange="toggleExcessLuggageDescription()">
                                                    <label for="excess_luggage_roundtrip"
                                                        class="ml-2 block text-sm font-medium text-gray-700">
                                                        {{ __('customer/busroot.excess_luggage', ['dimensions' => '60X45X50', 'weight' => '20kg', 'fee' => 'TSh. 2,500']) }}
                                                    </label>
                                                </div>
                                                <div id="excessLuggageDescriptionField" class="hidden mt-2">
                                                    <label for="excess_luggage_description" class="block text-sm font-medium text-gray-700 mb-1">
                                                        Excess Luggage Description
                                                    </label>
                                                    <input type="text" name="excess_luggage_description" id="excess_luggage_description"
                                                           class="text-gray-800 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                           placeholder="e.g., 1 extra bag, large box">
                                                </div>
                                                <!-- Age Group -->
                                                <div>
                                                    <label for="age_group_0_0"
                                                        class="block text-sm font-semibold text-gray-800">{{ __('customer/busroot.age_group') }}<span
                                                            class="text-red-500">*</span></label>
                                                    <select name="age_group" id="age_group_0_0"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        required>
                                                        <option value="Adult">{{ __('customer/busroot.adult') }}</option>
                                                        <option value="Child">{{ __('customer/busroot.child') }}</option>
                                                        <option value="Senior">{{ __('customer/busroot.senior') }}</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="discount_0_0"
                                                        class="block text-sm font-semibold text-gray-800">{{ __('customer/busroot.discount_coupon') }}</label>
                                                    <input type="text" id="discount_0_0" name="discount"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                </div>
                                                <div>
                                                    <label for="selectedSeatClass_0_0"
                                                        class="block text-sm font-semibold text-gray-800">{{ __('customer/busroot.seat_class') }}</label>
                                                    <input type="text" id="selectedSeatClass_0_0"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 bg-gray-50"
                                                        value="{{ __('customer/busroot.normal_seat') }}" readonly>
                                                </div>
                                                <div>
                                                    <label for="selectedSeatFare_0_0"
                                                        class="block text-sm font-semibold text-gray-800">{{ __('customer/busroot.fare') }}</label>
                                                    <input type="text" id="selectedSeatFare_0_0"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 bg-gray-50"
                                                        value="{{ convert_money($price) ?? __('customer/busroot.na') }}"
                                                        readonly>
                                                </div>
                                                @livewire('temp')
                                                @if (isset($distance) && $distance > 99)
                                                    @if (isset($info['travel_date']) && $info['travel_date'] != date('Y-m-d'))
                                                        @php
                                                            date_default_timezone_set('Africa/Nairobi');
                                                            $currentDate = date('Y-m-d');
                                                            try {
                                                                $travelDate = \Carbon\Carbon::parse($info['travel_date'])->format('Y-m-d');
                                                                $minDate =
                                                                    $travelDate >= $currentDate
                                                                        ? $travelDate
                                                                        : $currentDate;
                                                            } catch (Exception $e) {
                                                                $minDate = $currentDate;
                                                            }
                                                        @endphp
                                                        <div>
                                                            <label for="Insurance"
                                                                class="block text-sm font-semibold text-gray-800">{{ __('customer/busroot.insurance', ['amount' => 'TSh.3700']) }}</label>
                                                            <div class="mt-1">
                                                                <input type="checkbox" id="Insurance" name="Insurance"
                                                                    value="1" class="mr-2"
                                                                    onchange="toggleDateInput()">
                                                                <select name="type" id="type"
                                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 hidden mt-2">
                                                                    <option value="local">
                                                                        {{ __('customer/busroot.local') }}</option>
                                                                    <option value="foreign">
                                                                        {{ __('customer/busroot.foreign') }}</option>
                                                                </select>
                                                                <input type="date" id="insuranceDate"
                                                                    name="insuranceDate"
                                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 hidden mt-2"
                                                                    min="{{ $minDate }}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <p class="text-sm text-gray-500">
                                                {{ __('customer/busroot.boarding_point_and_time') }}</p>
                                            <p class="font-medium text-gray-800">
                                                {{ $time['start'] ?? __('customer/busroot.na') }}</p>
                                            <p class="text-sm text-gray-500">{{ __('customer/busroot.plate_no') }}
                                                {{ $car->bus_number ?? __('customer/busroot.na') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price Details Column -->
                                <div>
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h6 class="text-lg font-bold text-gray-900 mb-3">
                                            {{ __('customer/busroot.price_summary') }}</h6>
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">{{ __('customer/busroot.discount') }}</span>
                                                <span class="text-gray-800">{{ $currency }}. 0.00</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span
                                                    class="text-gray-600">{{ __('customer/busroot.system_charge') }}</span>
                                                <span class="text-gray-800">{{ $currency }}.
                                                    {{ convert_money($fees) }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">{{ __('customer/busroot.bus_fare') }}</span>
                                                <span class="text-gray-800">{{ $currency }}.
                                                    {{ convert_money($price) }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm font-bold">
                                                <span
                                                    class="text-gray-600">{{ __('customer/busroot.total_payable') }}</span>
                                                <span class="text-gray-800" id="total_payable_amount_roundtrip">{{ $currency }}.
                                                    {{ convert_money($fees + $price) }}</span>
                                            </div>
                                        </div>
                                        <button type="submit"
                                            class="w-full mt-4 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-medium py-3 px-6 rounded-lg shadow-md transition-all duration-300 flex items-center justify-center"
                                            onclick="submitPassengerDetails()">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            {{ __('customer/busroot.continue_to_payment') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function toggleDateInput() {
            const checkbox = document.getElementById('Insurance');
            const dateInput = document.getElementById('insuranceDate');
            const typeSelect = document.getElementById('type');
            const travelDate = "{{ $info['travel_date'] ?? '' }}";
            const currentDate = "{{ date('Y-m-d') }}";

            if (checkbox.checked) {
                dateInput.classList.remove('hidden');
                typeSelect.classList.remove('hidden');
                const minDate = travelDate && travelDate >= currentDate ? travelDate : currentDate;
                dateInput.setAttribute('min', minDate);
            } else {
                dateInput.classList.add('hidden');
                typeSelect.classList.add('hidden');
                dateInput.value = '';
            }
        }

        function submitPassengerDetails() {
            // Optional: Add client-side validation or additional logic before submission
            document.querySelector('form').submit();
        }

        function toggleExcessLuggageDescription() {
            const excessLuggageCheckbox = document.getElementById('excess_luggage_roundtrip');
            const excessLuggageDescriptionField = document.getElementById('excessLuggageDescriptionField');
            if (excessLuggageCheckbox.checked) {
                excessLuggageDescriptionField.classList.remove('hidden');
            } else {
                excessLuggageDescriptionField.classList.add('hidden');
            }
            updateTotalPayableRoundtrip();
        }

        function updateTotalPayableRoundtrip() {
            const excessLuggageCheckbox = document.getElementById('excess_luggage_roundtrip');
            const totalPayableElement = document.getElementById('total_payable_amount_roundtrip');
            let currentTotal = parseFloat("{{ $fees + $price }}");
            const excessLuggageFee = 2500; // Defined in the controller later

            if (excessLuggageCheckbox.checked) {
                currentTotal += excessLuggageFee;
            }

            totalPayableElement.innerHTML = "{{ $currency }}. " + formatMoney(currentTotal);
        }

        function formatMoney(amount) {
            return amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    </script>
@endsection
