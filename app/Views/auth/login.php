<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-amber-100">
                <i class="fas fa-graduation-cap text-2xl text-amber-600"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                ເຂົ້າສູ່ລະບົບ
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                ລະບົບລົງທະບຽນນັກສຶກສາ
            </p>
        </div>
        <form class="mt-8 space-y-6" action="<?= BASE_URL ?>auth/process-login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Session::generateCSRF() ?>">
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">ຊື່ຜູ້ໃຊ້</label>
                    <input id="username" 
                           name="username" 
                           type="text" 
                           required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 focus:z-10 sm:text-sm" 
                           placeholder="ຊື່ຜູ້ໃຊ້"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div>
                    <label for="password" class="sr-only">ລະຫັດຜ່ານ</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-amber-500 focus:border-amber-500 focus:z-10 sm:text-sm" 
                           placeholder="ລະຫັດຜ່ານ">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" 
                           name="remember-me" 
                           type="checkbox" 
                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        ຈື່ການເຂົ້າສູ່ລະບົບ
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 btn-hover">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock text-amber-500 group-hover:text-amber-400"></i>
                    </span>
                    ເຂົ້າສູ່ລະບົບ
                </button>
            </div>
        </form>
    </div>
</div>