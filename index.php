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

	session_start();

	// create_site_top and create_site_menu is defined in
	// file general_functions.php
	require 'general_functions.php';

	create_site_top();
	create_top_menu();
?>

	<div class="mainpage_div">
	<h2 class="mainpage">Runosydän</h2>

	<p class="mainpage">Tervetuloa runosydan.net sivustolle!<br>
	<h3>Lyhyesti</h3>
	Sivuston tarkoituksena on toimia julkaisukanavana omille runoillesi
	ja kirjoitelmillesi.<br><br>
	Mikäli et vielä ole rekisteröitynyt, voit rekisteröityä sivustolle
	ylälaidasta löytyvän Kirjaudu-sivun kautta.<br><br>
	Jos haluat vain lukea runoilijoiden tuotoksia, voit selata niitä
	ilman rekisteröitymistä ylälaidan Runoilijat-sivun kautta.<br><br>

	Hauskoja hetkiä runouden parissa!<br>

	<h3>Palaute</h3>
	Sivustoon liittyviä vikailmoituksia sekä kehitysideoita voi lähettää 
	osoitteeseen
	<a href="mailto:aleksi.rasanen@runosydan.net">aleksi.rasanen@runosydan.net</a>. Monia ominaisuuksia puuttuu ja joitain mahdollisesti
	lisätään ajan saatossa, jos aikaa ja innostusta liikenee.<br>

	<h3>Teknistä tietoa</h3>
	Tämä sivu on toteutettu käyttäen PHP-ohjelmointikieltä ja tietokantana
	MySQL:ää. Sivuston lähdekoodit on julkaistu GNU AGPL -lisenssillä ja
	ne löytyvät <a href="http://github.com/stargazers/Runosydan/tree/master">GitHubista</a>. 
	Kirjautuneille käyttäjille näkyvissä olevat ikonit runoja lisätessä/muokatessa sekä poistaessa
	on tehnyt Everaldo Coelho ja ne löytyvät Crystal Clear -nimisestä paketista ja ovat 
	LGPL-lisenssin alaisia.<br>

	<h3>Rekisteriseloste</h3>
	Voit katsoa rekisteriselosteen <a href="privacy_policy.php">täältä</a>.<br><br>
	</div>
</body>
</html>
