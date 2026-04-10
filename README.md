# PHP_Laravel12_Select2_Implement_Using_Alpine.JS

## Introduction

The **PHP_Laravel12_Select2_Implement_Using_Alpine.JS** project demonstrates how to implement **multi-select dropdowns with Select2** in a **Laravel 12 application**, using **Alpine.js** for reactive UI interactions. This project allows users to **create, view, edit, and delete products**, each of which can be associated with multiple categories stored as a JSON array in the database.  

With **Select2 integration**, the multi-select dropdown provides a **user-friendly, searchable, and pill-styled selection experience**, while Alpine.js enhances interactivity, such as confirmation dialogs for deletions and dynamic form handling. The project uses **Laravel’s Eloquent ORM** for database interactions and **Tailwind CSS** for a clean, responsive, and modern design.

---

## Project Overview

- Users can **list all products** with their details, including name, description, and associated categories.  
- Users can **create new products**, selecting one or more categories from a multi-select dropdown powered by **Select2**.  
- Users can **edit existing products**, updating their name, description, or selected categories.  
- Users can **view product details** on a dedicated page.  
- Users can **soft delete products** with confirmation handled via Alpine.js.  
- Categories are stored in the **products table as a JSON column**, allowing flexible association of multiple categories per product.  
- The UI is styled with **Tailwind CSS**, and the forms are enhanced with **Select2 for multi-selection** and **Alpine.js for interactivity**.   

---

## Tech Stack

* Laravel 12  
* PHP 8.2+  
* MySQL  
* Blade Templates  
* Alpine.js (via NPM + Vite)  
* Tailwind CSS  

---

## Project Name

```
PHP_Laravel12_Select2_Implement_Using_Alpine.JS
```

---

## Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Select2_Implement_Using_Alpine.JS "12.*"
cd PHP_Laravel12_Select2_Implement_Using_Alpine.JS
```

---

## Step 2: Database Configuration

Edit `.env` file:

```env
DB_DATABASE=laravel12_select2_alpine
DB_USERNAME=root
DB_PASSWORD=
```

Create database manually and then run:

```bash
php artisan migrate
```

---

## Step 3: Install Frontend Dependencies

Install Node packages:

```bash
npm install
```

Install Alpine.js:

```bash
npm install alpinejs
```

---

## Step 4: Configure Alpine.js with Vite

Edit `resources/js/app.js`

```js
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
```

Run Vite:

```bash
npm run dev
```

---

## Step 5: Create Model & Migration

We will store **multiple selected categories** using a JSON column.

```bash
php artisan make:model Product -m
```

### Migration

`database/migrations/xxxx_create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->text('description')->nullable();   // Product description
            $table->json('categories');                // Multiple selected categories

            $table->timestamps();
            $table->softDeletes();                     // Soft delete support
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};```

Run migration:

```bash
php artisan migrate
```

---

## Step 6: Model Configuration

`app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'categories',
    ];

    protected $casts = [
        'categories' => 'array',
    ];
}
```

---

## Step 7: Create Controller

```bash
php artisan make:controller ProductController
```

`app/Http/Controllers/ProductController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display all products
     */
    public function index()
    {
        $products = Product::latest()->get();

        return view('products.index', compact('products'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = ['Electronics', 'Fashion', 'Books', 'Furniture', 'Sports'];

        return view('products.create', compact('categories'));
    }

    /**
     * Store product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'categories'  => 'required|array',
        ]);

        Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'categories'  => $request->categories,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product saved successfully');
    }

    /**
     * Show single product
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = ['Electronics', 'Fashion', 'Books', 'Furniture', 'Sports'];

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'categories'  => 'required|array',
        ]);

        $product->update([
            'name'        => $request->name,
            'description' => $request->description,
            'categories'  => $request->categories,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Soft delete product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
```

---

## Step 8: Routes

`routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/


// List all products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Create product
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// Show single product
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Edit product
Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

// Delete product
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
```

---

## Step 9: Blade Views

### create.blade.php

`resources/views/products/create.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Product</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Select2 CSS & jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Pillbox style for selected items */
        .select2-selection__choice {
            background-color: #2563eb !important;
            color: white !important;
            border: none !important;
            padding: 0 8px !important;
            margin-right: 5px !important;
            border-radius: 12px !important;
            font-size: 0.9rem;
        }
        .select2-selection__choice__remove {
            color: white !important;
            margin-right: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-center">Create Product</h1>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                   required>
            @error('name') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Description</label>
            <textarea name="description"
                      class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                      rows="4">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Categories Multi-Select -->
        <div class="mb-6">
            <label class="block font-medium mb-1">Categories</label>
            <select name="categories[]" class="js-example-basic-multiple w-full" multiple="multiple">
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ (collect(old('categories'))->contains($category)) ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
            @error('categories') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow transition">
                Save
            </button>
            <a href="{{ route('products.index') }}" class="ml-4 text-gray-700 hover:underline">Back</a>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('.js-example-basic-multiple').select2({
        placeholder: 'Select categories',
        width: '100%',
        allowClear: true,
        closeOnSelect: true
    });
});
</script>

</body>
</html>
```

### index.blade.php

`resources/views/products/index.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold">Products</h1>
        <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Add Product</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categories</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $index => $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $product->description ?? 'No description' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ implode(', ', $product->categories) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('products.show', $product->id) }}" class="text-blue-600 hover:underline">View</a>
                                <a href="{{ route('products.edit', $product->id) }}" class="text-yellow-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('products.destroy', $product->id) }}" x-data @submit.prevent="if(confirm('Are you sure you want to delete this product?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
```

### edit.blade.php

`resources/views/products/edit.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Select2 CSS & jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-selection__choice {
            background-color: #2563eb !important;
            color: white !important;
            border: none !important;
            padding: 0 5px !important;
        }
        .select2-selection__choice__remove {
            color: white !important;
            margin-right: 3px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-center">Edit Product</h1>

    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                   class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400"
                   required>
            @error('name') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block font-medium mb-1">Description</label>
            <textarea name="description"
                      class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400"
                      rows="4">{{ old('description', $product->description) }}</textarea>
            @error('description') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Categories Multi-Select -->
        <div class="mb-6">
            <label class="block font-medium mb-1">Categories</label>
            <select name="categories[]" id="categories" multiple class="w-full">
                @foreach($categories as $category)
                    <option value="{{ $category }}"
                        {{ (is_array(old('categories', $product->categories)) && in_array($category, old('categories', $product->categories))) ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
            @error('categories') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center">
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded shadow transition">
                Update
            </button>
            <a href="{{ route('products.index') }}" class="ml-4 text-gray-700 hover:underline">Back</a>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#categories').select2({
        placeholder: 'Select categories',
        width: '100%',
        tags: false,
        allowClear: true,
        closeOnSelect: false
    });
});
</script>

</body>
</html>
```

### show.blade.php

`resources/views/products/show.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Product Details</h1>

    <p><strong>Name:</strong> {{ $product->name }}</p>
    <p class="mt-2"><strong>Description:</strong> {{ $product->description ?? 'N/A' }}</p>
    <p class="mt-2"><strong>Categories:</strong> {{ implode(', ', $product->categories) }}</p>

    <a href="{{ route('products.index') }}" class="inline-block mt-4 border px-4 py-2 rounded">Back</a>
</div>

</body>
</html>
```


---

## Step 10: Run the Application

### Terminal 1

```bash
php artisan serve
```

### Terminal 2

```bash
npm run dev
```

Visit:

```
http://127.0.0.1:8000/products
```

---

## Project Structure

```
PHP_Laravel12_Select2_Implement_Using_Alpine.JS/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ProductController.php
│   └── Models/
│       └── Product.php
│
├── database/
│   └── migrations/
│       └── 2026_01_30_000000_create_products_table.php
│
├── resources/
│   ├── js/
│   │   └── app.js   //import alpine
│   └── views/
│       └── products/
│           ├── index.blade.php     <!-- List all products -->
│           ├── create.blade.php    <!-- Create new product -->
│           ├── edit.blade.php      <!-- Edit existing product -->
│           └── show.blade.php      <!-- View product details -->
│
├── routes/
│   └── web.php
├── package.json
├── vite.config.js
└── .env
```

---

##  Output

### Index Page

<img width="1814" height="1087" alt="Screenshot 2026-01-30 124949" src="https://github.com/user-attachments/assets/9c7210d3-fc1a-4715-934b-52eff2df687d" />


### Create Page

<img width="1806" height="1082" alt="Screenshot 2026-01-30 125057" src="https://github.com/user-attachments/assets/612d4f11-7682-4f5f-8e54-b0c8d4b3cd81" />

<img width="1814" height="1090" alt="Screenshot 2026-01-30 125118" src="https://github.com/user-attachments/assets/88d3520c-e8ce-4cba-99d2-84f59ce1e867" />

### Show Page

<img width="1814" height="1089" alt="Screenshot 2026-01-30 125132" src="https://github.com/user-attachments/assets/ffabf3ff-9f0d-4f46-aa14-5eedfd6a1ad3" />

### Edit Page

<img width="1810" height="1084" alt="Screenshot 2026-01-30 125149" src="https://github.com/user-attachments/assets/a6858341-a538-4a69-9d5b-c464b1a1ad51" />

<img width="1813" height="1085" alt="Screenshot 2026-01-30 125201" src="https://github.com/user-attachments/assets/d257b323-f71e-4963-bff7-3bcaef0dd41d" />

### Delete Page

<img width="1814" height="1086" alt="Screenshot 2026-01-30 125213" src="https://github.com/user-attachments/assets/cbffec2b-d955-4c17-a592-f0ec192a550a" />

<img width="1813" height="1088" alt="Screenshot 2026-01-30 125225" src="https://github.com/user-attachments/assets/a7fcffc6-67b8-4782-986b-13f53b996d51" />

---

Your **PHP_Laravel12_Select2_Implement_Using_Alpine.JS** project is now ready!
<<<<<<< HEAD


=======
>>>>>>> development
