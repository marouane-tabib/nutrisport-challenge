<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle commande</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h1 style="color: #2c3e50; margin-top: 0;">Nouvelle commande</h1>
        
        <p>Une nouvelle commande vient d'être passée.</p>
        
        <div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Commande n°</strong> : {{ $order->id }}</p>
            <p style="margin: 5px 0;"><strong>Client</strong> : {{ $order->user->full_name }}</p>
            <p style="margin: 5px 0;"><strong>Site</strong> : {{ $order->site->name }}</p>
            <p style="margin: 5px 0;"><strong>Total</strong> : {{ number_format($order->total, 2) }} €</p>
            <p style="margin: 5px 0;"><strong>Date</strong> : {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        
        <h2 style="color: #2c3e50; margin-top: 30px;">Détail des produits</h2>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background-color: white;">
            <thead>
                <tr style="background-color: #3490dc; color: white;">
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Produit</th>
                    <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Quantité</th>
                    <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">Prix unitaire</th>
                    <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td style="padding: 12px; border: 1px solid #ddd;">{{ $item->product->name }}</td>
                    <td style="padding: 12px; text-align: center; border: 1px solid #ddd;">{{ $item->quantity }}</td>
                    <td style="padding: 12px; text-align: right; border: 1px solid #ddd;">{{ number_format($item->unit_price, 2) }} €</td>
                    <td style="padding: 12px; text-align: right; border: 1px solid #ddd;">{{ number_format($item->quantity * $item->unit_price, 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
