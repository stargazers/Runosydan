<?php

/*
Main page. Part of Runosydan.net.
Copyright (C) 2009	Aleksi Räsänen <aleksi.rasanen@runosydan.net>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	function show_news()
	{
		if( file_exists( 'news.txt' ) )
		{
			echo '<h3>Uutisia</h3>';
			echo '<div class="textblock">';
			$data = file( 'news.txt' );
			$rows = count( $data );
			$nextIsHeader = true;
			$headers = 0;

			for( $i=0; $i < $rows; $i++ )
			{ 
				// If next line is header, then it must bold font
				if( $nextIsHeader )
				{
					echo '<div class="news_item_header">';

					// Show only three newest items
					if( $headers == 3 )
					{
						echo '<a href="news.php">Kaikki uutiset</a>';
						break;
					}

					// Increase number of shown news
					$headers++;

					echo '<b>' . $data[$i] . '</b>';
					$nextIsHeader = false;

					echo '</div>';

				}
				else
				{
					echo '<div class="news_item">';
					if( strstr( $data[$i], '-----' ) )
						$nextIsHeader = true;
					else
						echo $data[$i];
					echo '</div>';
				}
				
			}

			echo '</div>';
		}
	}

	session_start();

	// create_site_top and create_site_menu is defined in
	// file general_functions.php
	require 'general_functions.php';

	create_site_top();
	create_top_menu();
?>

	<div class="mainpage_div">

	<h3>Runosydän.net lyhyesti</h3>
	<div class="textblock">
	Sivuston tarkoituksena on toimia julkaisukanavana omille runoillesi
	ja kirjoitelmillesi.<br><br>
	Mikäli et vielä ole rekisteröitynyt, voit rekisteröityä sivustolle
	ylälaidasta löytyvän Kirjaudu-sivun kautta.
	Jos haluat vain lukea runoilijoiden tuotoksia, voit selata niitä
	ilman rekisteröitymistä ylälaidan Runoilijat-sivun kautta.<br><br>

	Hauskoja hetkiä runouden parissa!<br><br>
	</div>

	<hr>
	<?php show_news() ?>

	<br> <br>
	<hr>
	<h3>Miksi sivusto on olemassa?</h3>
	<div class="textblock">
	Tällä hetkellä on netissä useampiakin sivustoja joissa käyttäjät
	voivat julkaista omia kirjoituksiaan. Tämä sivusto on tehty siitäkin
	huolimatta, mutta erilaisia tarpeita vastaaviksi. Tarpeet joita
	itselläni oli runosivustolta:
	<ul>
		<li><a href="http://fsfe.org/about/basics/freesoftware.fi.html" target="_new">Vapaa lähdekoodi</a> 
		(<a href="http://www.gnu.org/licenses/agpl-3.0.txt" target="_new">GNU AGPL</a>)</li>
		<li>Ei rajoituksia runojen määrälle päivää kohden</li>
		<li>Ei rajoituksia runojen kokonaismäärälle</li>
		<li>Mahdollisuus ladata kaikki omat runot 
		helposti tekstitiedostona ilman että joudun selaamaan
		kaikki sivut yksi kerrallaan läpi (varmuuskopiointia varten
		sekä jos tahdon vaihtaa palvelusta toiseen)</li>
		<li>Satunnainen runo -toiminto</li>
	</ul>
	</div>

	<h3>Palaute</h3>
	<div class="textblock">
	Sivustoon liittyviä vikailmoituksia sekä kehitysideoita voi lähettää 
	osoitteeseen
	<a href="mailto:aleksi.rasanen@runosydan.net">aleksi.rasanen@runosydan.net</a>. Monia ominaisuuksia puuttuu ja joitain mahdollisesti
	lisätään ajan saatossa, jos aikaa ja innostusta liikenee.<br>
	</div>

	<h3>Teknistä tietoa</h3>
	<div class="textblock">
	Tämä sivu on toteutettu käyttäen PHP-ohjelmointikieltä ja tietokantana
	MySQL:ää. Javascriptissä on käytetty jQuery-kirjastoa ja 
	sivuston lähdekoodit on julkaistu GNU AGPL -lisenssillä ja
	ne löytyvät <a href="http://github.com/stargazers/Runosydan/tree/master">GitHubista</a>. 
	Kirjautuneille käyttäjille näkyvissä olevat ikonit runoja lisätessä/muokatessa sekä poistaessa
	on tehnyt Everaldo Coelho ja ne löytyvät Crystal Clear -nimisestä paketista ja ovat 
	LGPL-lisenssin alaisia. Sivujen ohjelmointi on tehty käyttäen VIM-editoria.<br>
	</div>

	<h3>Rekisteriseloste</h3>
	<div class="textblock">
	Voit katsoa rekisteriselosteen <a href="privacy_policy.php">täältä</a>.<br><br>
	</div>
	</div>

	<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	try {
	var pageTracker = _gat._getTracker("UA-12163481-2");
	pageTracker._trackPageview();
	} catch(err) {}</script>
</body>
</html>
