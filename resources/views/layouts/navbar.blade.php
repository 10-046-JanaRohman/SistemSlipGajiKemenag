<header class="bg-white shadow">

    <div class="flex justify-between items-center p-6">

        <div>

            <h1 class="text-2xl font-bold">

                Dashboard

            </h1>

        </div>

        <div class="flex items-center gap-4">

            <div class="text-right">

                <h3 class="font-semibold">

                    {{ auth()->user()->name }}

                </h3>

                <p class="text-gray-500">

                    {{ ucfirst(auth()->user()->role) }}

                </p>

            </div>

            <div class="w-12 h-12 rounded-full bg-green-700 text-white flex items-center justify-center font-bold">

                {{ strtoupper(substr(auth()->user()->name,0,1)) }}

            </div>

        </div>

    </div>

</header>