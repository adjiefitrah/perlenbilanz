<html>
<head>
	<style>
		@page {
			margin-left:2.5cm;
		}
	</style>
	<title><?php if ($_['verkauf']->plattform): p($_['verkauf']->plattform); ?> - <? endif; ?>Rechnung Nr. <?php p($_['verkauf']->rechnungsnummer) ?></title></head>
<body>
<span style="font-family: sans; font-size: 7px;">
	<?php if ($_['verkauf']->plattform): p($_['verkauf']->plattform); ?> - <? endif; ?>
	Nina Obermeyer, Am Ginsterbusch 51, 30459 Hannover<br/>
</span><br/>
<table width="100%">
<tbody>
<tr>
	<td valign="top"><!-- Anschrift -->
	<?php print_unescaped($_['verkauf']->rechnungsanschrift); ?>
	</td>

	<td align="right"><!-- Absender -->
	<?php if ($_['verkauf']->plattform): p($_['verkauf']->plattform); ?><br/><?php endif; ?>
	<br/>
	Nina Obermeyer<br/>
	<br/>
	Am Ginsterbusch 51<br/>
	<br/>
	30459 Hannover<br/>
	<br/>
	<?php if ($_['verkauf']->plattform === 'fancywork'): ?>
		E-Mail: shop_fancywork@gmx.de<br/>
	<?php elseif ($_['verkauf']->plattform === 'HobbyQuelle'): ?>
		E-Mail: hobbyquelle@gmx.de<br/>
	<?php else: ?>
		E-Mail: ninaobermeyer@gmx.de<br/>
	<?php endif; ?>
	<br/>
	Steuernummer: 26/132/03735<br/>
	<br/>
	Datum: <?php p($_['datum']) ?>
	</td>
</tr>
</tbody>
</table>
Rechnung Nr. <?php p($_['verkauf']->rechnungsjahr) ?>-<?php p(sprintf("%03s", $_['verkauf']->rechnungsnummer)) ?><br/>
<br/>
<br/>
<br/>
Sehr geehrte Kundin, sehr geehrter Kunde,<br/>
<br/>
für Ihre Bestellung<?php if ($_['verkauf']->bestellnummer): ?>
<!-- Bestellnummer nur bei Dawanda anzeigen, extra Bestellnummerfeld-->
 mit der Dawanda-Bestellnummer <?php p($_['verkauf']->bestellnummer) ?>
<?php endif; ?> bedanke ich mich und berechne für meine Leistungen:<br/>
<br/>
<br/>
<table style="width:100%; border-spacing:0; border-collapse: collapse;">
	<thead>
	<tr>
		<th style="border-right:1px solid black; border-bottom:2px solid black;">Ware / Leistung</th>
		<th style="border-right:1px solid black; border-bottom:2px solid black;">Menge</th>
		<th style="border-right:1px solid black; border-bottom:2px solid black;">Einzelpreis brutto</th>
		<th style="border-right:1px solid black; border-bottom:2px solid black;">Gesamt</th>
		<th style="border-bottom:2px solid black;">Steuersatz</th>
	</tr>
	</thead>
	<!-- Positionen -->
	<tbody>
	<?php foreach($_['positionen'] as $position) { ?>
	<tr>
		<td style="border-right:1px solid black; border-bottom:1px solid black;">
			<?php if ($position->bezeichnung): ?>
			<?php print_unescaped($position->bezeichnung); ?>
			<?php else: ?>
			<?php print_unescaped($position->typ); ?>
			<?php endif; ?>
		</td>
		<td style="border-right:1px solid black; border-bottom:1px solid black;" align="center">
			<?php p($position->stueck); ?>
		</td>
		<td style="border-right:1px solid black; border-bottom:1px solid black;" align="right">
			<?php p(number_format($position->brutto,2,',','.')); ?> €
		</td>
		<td style="border-right:1px solid black; border-bottom:1px solid black;" align="right">
			<?php p(number_format($position->stueck * $position->brutto,2,',','.')); ?> €
		</td>
		<td style="border-bottom:1px solid black;" align="right">
			<?php p($position->mwstProzent); ?>%
		</td>
	</tr>
	<?php } ?>
	<!-- summenzeile -->
	<tr>
		<td style="border-right:1px solid black;" colspan="3" align="right">Rechnungsbetrag netto</td>
		<td style="border-right:1px solid black;" align="right"><?php p(number_format($_['verkauf']->netto,2,',','.')) ?> €</td>
		<td></td>
	</tr>
	<tr>
		<td style="border-right:1px solid black;" colspan="3" align="right">19% Umsatzsteuer</td>
		<td style="border-right:1px solid black;" align="right"><?php p(number_format($_['verkauf']->mwst,2,',','.')) ?> €</td>
		<td></td>
	</tr>
	<tr>
		<td style="border-top:3px double black; border-right:1px solid black;" colspan="3" align="right"><b>Rechnungsbetrag brutto</b></td>
		<td style="border-top:3px double black; border-right:1px solid black;" align="right"><b><?php p(number_format($_['verkauf']->brutto,2,',','.')) ?> €</b></td>
		<td style="border-top:3px double black;"></td>
	</tr>
	</tbody>
</table>
<br/>
<?php if ($_['verkauf']->wertstellung): ?>
Die Zahlung des Rechnungsbetrages ist mit Wertstellung zum <?php p($_['verkauf']->wertstellung) ?> per <?php p($_['verkauf']->zahlweise) ?> erfolgt.<br/>
<?php endif ?>
<br/>
Das Rechnungsdatum entspricht dem Versanddatum.<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
Mit freundlichen Grüßen<br/>
Nina Obermeyer
<!-- verkauf:
<?php print_r($_['verkauf']); ?>
-->
<!-- positionen:
<?php print_r($_['positionen']); ?>
-->
</body>
</html>