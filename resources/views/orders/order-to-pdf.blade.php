<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 24px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 24px;
        }

        section {
            max-width: 900px;
            margin: 0 auto;
        }

        .section-group {
            margin-bottom: 28px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        p {
            font-size: 14px;
            margin: 4px 0;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        thead tr {
            background-color: #f3f4f6;
        }

        th,
        td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }

        th {
            font-weight: 600;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .product-cell img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            display: block;
        }
    </style>
</head>

<body>
    <h1>Receipt</h1>

    <section>
        <div class="section-group">
            <h3 class="section-title">Customer Information</h3>
            <p>NIF: {{ $order->nif }}</p>
            <p>Name: {{ $order->customer->user->name }}</p>
            <p>Email: {{ $order->customer->user->email }}</p>
            <p>Address: {{ $order->address }}</p>
        </div>

        <div class="section-group">
            <h3 class="section-title">Order Details</h3>
            <p>Date: {{ $order->date }}</p>
            <p>Status: {{ $order->status }}</p>
            <p>Total Price: {{ $order->total_price }}€</p>
        </div>

        <div class="section-group">
            <h3 class="section-title">Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Color</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->order_items as $item)
                        <tr>
                            <td class="product-cell">
                                <img src="{{ $item->tshirt_image->imageEncode64 }}" alt="{{ $item->tshirt_image->name }}">
                                <br>{{ $item->tshirt_image->name }}
                            </td>
                            <td>{{ $item->color->name ?? $item->color_code }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->unit_price }}€</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</body>
