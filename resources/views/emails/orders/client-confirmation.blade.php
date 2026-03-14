<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande confirmée</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h1 style="color: #2c3e50; margin-top: 0;">Commande confirmée</h1>
        
        <p>Bonjour <strong>{{ $order->user->full_name }}</strong>,</p>
        
        <p>Votre commande n° <strong>{{ $order->id }}</strong> a bien été enregistrée.</p>
        
        <h2 style="color: #2c3e50; margin-top: 30px;">Détails de la commande</h2>
        
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
        
        <p style="font-size: 18px; font-weight: bold; text-align: right; margin: 20px 0;">Total : {{ number_format($order->total, 2) }} €</p>
        
        <div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #2c3e50; margin-top: 0;">Adresse de livraison</h3>
            <p style="margin: 5px 0;">{{ $order->shipping_full_name }}</p>
            <p style="margin: 5px 0;">{{ $order->shipping_address }}</p>
            <p style="margin: 5px 0;">{{ $order->shipping_city }}</p>
            <p style="margin: 5px 0;">{{ $order->shipping_country }}</p>
        </div>
        
        <p>Statut de la commande : <strong>{{ $order->status->value }}</strong></p>
        
        <p>Merci de votre confiance.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.frontend_url') }}/orders/{{ $order->id }}" style="background-color: #3490dc; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Suivre ma commande</a>
        </div>
    </div>
</body>
</html>
