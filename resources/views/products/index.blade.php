<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 12px;
            margin-right: 4px;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
        }

        .btn-blue { background: #2563eb; color: white; }
        .btn-gray { background: #6b7280; color: white; }
        .btn-red { background: #dc2626; color: white; }

        .table-row:hover {
            background: #f9fafb;
        }
    </style>
</head>

<body class="bg-gray-100 p-6">

<div class="max-w-7xl mx-auto">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">📦 Products</h1>
            <p class="text-gray-500 text-sm">Manage all products</p>
        </div>

        <a href="{{ route('products.create') }}" class="btn btn-blue">
            + Add Product
        </a>
    </div>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- FILTER -->
    <div class="card p-4 mb-5">
        <form method="GET" class="flex gap-3">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search product..."
                   class="border px-3 py-2 rounded w-1/3">

            <select name="category" class="border px-3 py-2 rounded w-1/3">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-blue">Filter</button>

            <a href="{{ route('products.index') }}" class="btn btn-gray">Reset</a>

            <a href="{{ route('products.trash') }}" class="btn btn-red">Trash</a>

        </form>
    </div>

    <!-- TABLE -->
    <div class="card overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Description</th>
                    <th class="px-6 py-3 text-left">Categories</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($products as $index => $product)
                <tr class="table-row">

                    <!-- FIXED PAGINATION NUMBER -->
                    <td class="px-6 py-4">
                        {{ $products->firstItem() + $index }}
                    </td>

                    <td class="px-6 py-4 font-semibold">
                        {{ $product->name }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $product->description ?? 'No description' }}
                    </td>

                    <td class="px-6 py-4">
                        @foreach($product->categories as $cat)
                            <span class="badge">{{ $cat }}</span>
                        @endforeach
                    </td>

                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('products.show', $product->id) }}" class="text-blue-600">View</a>
                        <a href="{{ route('products.edit', $product->id) }}" class="text-yellow-600 ml-2">Edit</a>

                        <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 ml-2" onclick="return confirm('Are You Sure Delete This Product?')">
                                Delete
                            </button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-500">
                        No products found
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <!-- PAGINATION -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>

</div>

</body>
</html>