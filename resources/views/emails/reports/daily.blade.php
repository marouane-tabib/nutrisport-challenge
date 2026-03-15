<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport quotidien NutriSport</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
        <h1 style="color: #2c3e50; margin-top: 0;">Rapport quotidien NutriSport</h1>
        
        <p>Voici le rapport des ventes pour la journée du <strong>{{ $reportData['date'] }}</strong>.</p>
        
        <div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h2 style="color: #2c3e50; margin-top: 0; font-size: 16px;">Résumé des ventes</h2>
            
            @if ($reportData['most_sold_product'])
            <p style="margin: 5px 0;"><strong>Produit le plus vendu</strong> : {{ $reportData['most_sold_product']['name'] }} ({{ $reportData['most_sold_product']['quantity'] }} unités)</p>
            @endif
            
            @if ($reportData['least_sold_product'])
            <p style="margin: 5px 0;"><strong>Produit le moins vendu</strong> : {{ $reportData['least_sold_product']['name'] }} ({{ $reportData['least_sold_product']['quantity'] }} unités)</p>
            @endif
            
            @if ($reportData['highest_revenue_product'])
            <p style="margin: 5px 0;"><strong>Produit avec le plus haut revenu</strong> : {{ $reportData['highest_revenue_product']['name'] }} ({{ number_format($reportData['highest_revenue_product']['revenue'], 2) }} €)</p>
            @endif
            
            @if ($reportData['lowest_revenue_product'])
            <p style="margin: 5px 0;"><strong>Produit avec le plus bas revenu</strong> : {{ $reportData['lowest_revenue_product']['name'] }} ({{ number_format($reportData['lowest_revenue_product']['revenue'], 2) }} €)</p>
            @endif
        </div>
        
        <h2 style="color: #2c3e50; margin-top: 30px;">Revenu par site</h2>
        
        @if (count($reportData['revenue_per_site']) > 0)
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background-color: white;">
            <thead>
                <tr style="background-color: #3490dc; color: white;">
                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Site</th>
                    <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">Revenu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData['revenue_per_site'] as $siteRevenue)
                <tr>
                    <td style="padding: 12px; border: 1px solid #ddd;">{{ $siteRevenue['site'] }}</td>
                    <td style="padding: 12px; text-align: right; border: 1px solid #ddd;">{{ number_format($siteRevenue['revenue'], 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #666; font-style: italic;">Aucune vente pour cette journée.</p>
        @endif
    </div>
</body>
</html>
