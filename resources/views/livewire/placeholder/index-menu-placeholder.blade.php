<div class="flex justify-center min-h-screen pb-2 pt-10 px-4">
    <div class="block w-full max-w-10xl">
        <div role="status" class="space-y-8 animate-pulse w-full">
            <!-- Header -->
            <div class="w-full mb-6">
                <!-- Breadcrumb -->
                <div class="h-3 bg-gray-200 rounded-full dark:bg-gray-700 w-1/4 mb-4"></div>
                
                <!-- Title -->
                <div class="h-5 bg-gray-300 rounded-full dark:bg-gray-600 w-1/6 mb-6"></div>
                
                <div class="flex">
                    <div class="grow mr-4 pr-32">
                        <!-- Search inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                            @foreach(range(1, 3) as $_)
                                <div>
                                    <div class="h-3 bg-gray-200 rounded-full dark:bg-gray-700 w-1/3 mb-2"></div>
                                    <div class="h-10 bg-gray-200 rounded-lg dark:bg-gray-700 w-full"></div>
                                </div>
                            @endforeach
                            <div class="min-[639px]:hidden">
                                <div class="h-10 bg-gray-200 rounded-lg dark:bg-gray-700 w-full"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Button -->
                    <div class="max-[639px]:hidden shrink-0 self-end mb-14">
                        <div class="h-10 bg-gray-300 rounded-lg dark:bg-gray-600 w-40"></div>
                    </div>
                </div>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow-sm rounded-lg">
                        <div class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Table Header -->
                            <div class="flex">
                                @foreach(range(1, 7) as $_)
                                    <div class="flex-1 p-6">
                                        <div class="h-4 bg-gray-300 rounded-full dark:bg-gray-600"></div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Table Body -->
                            @foreach(range(1, 8) as $_)
                                <div class="flex">
                                    @foreach(range(1, 7) as $index)
                                    <div class="flex-1 p-6">
                                        @if($index == 1)
                                        <!-- Primer elemento como cuadrado -->
                                        <div class="flex items-center justify-center">
                                            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded-sm"></div>
                                        </div>
                                        @else
                                        <!-- Otros elementos como círculos -->
                                        <div class="pt-[10px]">
                                            <div class="h-4 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                <div class="h-4 bg-gray-300 rounded-full dark:bg-gray-600 w-1/3"></div>
            </div>
        </div>
        
        <span class="sr-only">Cargando...</span>
    </div>
</div>