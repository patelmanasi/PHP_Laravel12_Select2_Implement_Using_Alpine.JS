<!DOCTYPE html>
<html>
<head>
    <title>Trash Products</title>
    @vite(['resources/css/app.css'])

    <style>
        body {
            background: linear-gradient(135deg, #eef2f7, #f8fafc);
        }

        .glass {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .btn {
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            transition: 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .btn-green {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
        }

        .btn-green:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(34,197,94,0.3);
        }

        .btn-red {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-red:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239,68,68,0.3);
        }

        .btn-gray {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        .btn-gray:hover {
            transform: translateY(-2px);
        }

        .item {
            transition: 0.3s ease;
            border-left: 4px solid transparent;
        }

        .item:hover {
            transform: translateY(-3px);
            border-left: 4px solid #6366f1;
            background: rgba(255,255,255,0.9);
        }

        .icon {
            font-size: 18px;
        }
    </style>
</head>

<body class="min-h-screen p-6">

<div class="max-w-5xl mx-auto">

    <!-- HEADER -->
    <div class="glass p-6 flex justify-between items-center mb-6">

        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                🗑️ Trash Products
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Restore or permanently delete removed products
            </p>
        </div>

        <a href="{{ route('products.index') }}" class="btn btn-gray">
            ← Back
        </a>

    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="glass p-4 mb-5 text-green-700 font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- EMPTY STATE -->
    @if($products->count() == 0)
        <div class="glass p-12 text-center text-gray-500">
            <div class="text-6xl mb-3">🗑️</div>
            <h2 class="text-xl font-semibold">Trash is empty</h2>
            <p class="text-sm mt-1">No deleted products found.</p>
        </div>
    @else

    <!-- LIST -->
    <div class="space-y-4">

        @foreach($products as $product)
            <div class="glass item p-5 flex justify-between items-center">

                <!-- LEFT -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $product->name }}
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        This product is in trash
                    </p>
                </div>

                <!-- RIGHT ACTIONS -->
                <div class="flex gap-3">

                    <!-- RESTORE -->
                    <a href="{{ route('products.restore', $product->id) }}"
                       class="btn btn-green"
                       onclick="return confirm('Restore this product?')">
                        ♻ Restore
                    </a>

                    <!-- DELETE FOREVER -->
                    <form method="POST"
                          action="{{ route('products.forceDelete', $product->id) }}"
                          onsubmit="return confirm('Permanently delete this product?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-red">
                            ❌ Delete
                        </button>

                    </form>

                </div>

            </div>
        @endforeach

    </div>

    @endif

</div>

</body>
</html>