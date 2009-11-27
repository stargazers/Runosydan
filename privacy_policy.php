<?php

/*
Privacy policy. Part of Runosydan.net.
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

require 'general_functions.php';

create_site_top();
create_top_menu();

echo '<div class="privacy_policy">';
echo '<h3>Rekisteriseloste</h3>';

echo '<h4>1. Rekisterinpitäjä</h4>';
echo '<p>Aleksi Räsänen<br>';
echo 'Silokkaantie 5 A21<br>';
echo '40640 Jyväskylä<br>';
echo '044-312 2385</p>';

echo '<h4>2. Rekisteriasioita hoitava yhteyshenkilö</h4>';
echo '<p>Sama kuin yllä</p>';

echo '<h4>3. Rekisterin nimi</h4>';
echo '<p>Käyttäjätunnusrekisteri</p>';

echo '<h4>4. Rekisterin käyttötarkoitus</h4>';
echo '<p>Palvelun ylläpitoon tarvittavat tiedot.</p>';

echo '<h4>5. Rekisterin tietosisältö</h4>';
echo '<p><b>Pakolliset tiedot:</b> käyttäjätunnus ja salasana.<br>';
echo '<b>Vapaaehtoiset tiedot:</b> Etunimi, sukunimi, kaupunki, kotisivu, sähköpostiosoite, syntmäpäivä.</p>';

echo '<h4>6. Säännönmukaiset tietolähteet</h4>';
echo '<p>Käyttäjien itsensä antamat tiedot</p>';

echo '<h4>7. Säännömukaiset tietojen luovutukset</h4>';
echo '<p>Tietoja ei luovuteta kolmansille osapuolille ilman virkavallan määräystä.</p>';

echo '<h4>8. Rekisterin suojauksen periaatteet</h4>';
echo '<p>Palvelimen tietokantaan pääsee käsiksi vain palvelun ylläpitäjä sekä palvelimen ylläpitäjä.</p>';

echo '</div>';

?>
