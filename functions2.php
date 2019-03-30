<?php

$dataLoaded = false;
$data;

function println($text) {
	echo $text . "<br/>";
}

function readyHTML($text) {
	return utf8_decode(htmlentities($text));
}

function getDataFromSheet() {
	$url = "https://docs.google.com/spreadsheet/pub?key=1PiP0a6eV8Vnc8_WzOqMhXxHTVtj3PFk8-6fN3Q664I8&output=csv";
    $file = fopen($url, 'r');
	$data = array();
	fgetcsv($file, ","); //skip the first line
    while (($col = fgetcsv($file, ",")) != false) {
  		$work = array();
		$work['Title'] = $col[0];
		$work['FileLocation'] = $col[1];
		$work['Author'] = $col[5];
		$work['Length'] = $col[6];
		$work['Words'] = $col[7];
		$work['Target'] = $col[8];
		$work['Chapter'] = $col[9];
		$work['ThumbnailImage'] = str_replace("\\", "/", $col[10]);
		$work['Description'] = $col[11];
		$work['Experiences'] = $col[12];
		$work['URL'] = $col[14];
		$work['Unavailable'] = $col[15];
		$work['Audio'] = $col[16];
		$work['AudioFinished'] = $col[17];
		$category = "";
		for($x = 2; $x <= 4; $x++){
			if($col[$x] != ""){
				if($x != 2) $category .= "/";
				$category .= $col[$x];
			} else continue;
		}
		$work['Category'] = $category;
		array_push($data, $work);
    }
    fclose($file);
	return $data;
}

function ensureDataLoaded() {
	global $dataLoaded, $data;
	if (!$dataLoaded) {
		//session_start();
		if (isset($_SESSION['HearATaleContent'])) {
			$GLOBALS['data'] = $_SESSION['HearATaleContent'];
			$dataLoaded = true;
		} else {
			$data = getDataFromSheet();
			$_SESSION['HearATaleContent'] = $data;
			$dataLoaded = true;
		}
	}
}

function getAllByAuthor($author){
	ensureDataLoaded();
	global $data;
	$matches = array();
	$titles = array();
	foreach ($data as $work){
		if($work['Author'] == $author && !in_array($work['Title'], $titles)){
			array_push($matches, $work);
			array_push($titles, $work['Title']);
		}
	}
	return $matches;
}

function getAllByAuthorOutOfPool($author, $pool){
	ensureDataLoaded();
	$matches = array();
	$titles = array();
	foreach ($pool as $work){
		if($work['Author'] == $author && !in_array($work['Title'], $titles)){
			array_push($matches, $work);
			array_push($titles, $work['Title']);
		}
	}
	return $matches;
}

function getAllByAuthorOutOfPool_absolute($author, $pool){
	ensureDataLoaded();
	$matches = array();
	foreach ($pool as $work){
		if($work['Author'] == $author){
			array_push($matches, $work);
		}
	}
	return $matches;
}

function getAllPartsOutOfPool($title, $pool){
	$matches = array();
	foreach($pool as $work){
		if($work['Title'] == $title){
			array_push($matches, $work);
		}
	}
	return $matches;
}

function getAllInCategory($query) {
	ensureDataLoaded();
	global $data;
	$matches = array();
	foreach ($data as $work) {
		//category is query or is in a subcategory of query
		if(categoryEqual($work['Category'], $query)){
			array_push($matches, $work);
		}
	}
	return $matches;
}

function categoryEqual($query, $against){
	return $query == $against || strpos($query, $against . '/') === 0;
}

function titleCarousel($category) {
	$data = getAllInCategory($category);
	shuffle($data);
	echo '<div class="owl-carousel">' . PHP_EOL;
	$max = 7;
	for ($x = 0; $x < min($max, count($data)); $x++) {
		$work = $data[$x];
		if ($work['ThumbnailImage'] == "" || $work['Title'] == "" || $work['FileLocation'] == ""){
			$max++;
			continue;
		}
		echo '<div class="carousel_item">' . PHP_EOL;
		echo '<a href="video.php?url=' . $work['FileLocation'] . '&youtubeurl=' . $work['URL'] . '">' . PHP_EOL;
		echo '<img src="Thumbnails/' . str_replace("\\", "/", $work['ThumbnailImage']) . '">' . PHP_EOL;
		echo '<div class="carousel_text">' . readyHTML($work['Title']) . '</div>' . PHP_EOL;
		echo '</a>' . PHP_EOL;
		echo '</div>' . PHP_EOL;
	}
	echo '</div>' . PHP_EOL;
}

function twoRowTitleCarousel($category) {
	$data = getAllInCategory($category);
	shuffle($data);
	echo '<div class="owl-carousel">' . PHP_EOL;
	$max = 14;
	for ($x = 0; $x < min($max, count($data)); $x++) {
		$work = $data[$x];
		if ($work['ThumbnailImage'] == "" || $work['Title'] == "" || $work['FileLocation'] == "" 
            || getimagesize('Thumbnails/' . str_replace("\\", "/", $work['ThumbnailImage']))[1] > 120){
            
			$max++;
			continue;
		}
		echo '<div class="carousel_item">' . PHP_EOL;
		echo '<a href="video.php?url=' . $work['FileLocation'] . '&youtubeurl=' . $work['URL'] . '">' . PHP_EOL;
		echo '<img src="Thumbnails/' . str_replace("\\", "/", $work['ThumbnailImage']) . '">' . PHP_EOL;
		echo '<div class="carousel_text">' . readyHTML($work['Title']) . '</div>' . PHP_EOL;
		echo '</a>' . PHP_EOL;
		echo '</div>' . PHP_EOL;
		if(($x - ($max - 14)) == 6){
			echo '</div><div class="owl-carousel">';
		}
	}
	echo '</div>' . PHP_EOL;
}

function authorCarousel($category) {
	$authorPage = ($category == "Southern Literature" ? "SOUTHERN_author.php" : "ADULT_author.php");
	$catData = getAllInCategory($category);
	$data = array();
	$authors = array();
    $southern = getAllInCategory("Southern Literature");
	foreach($catData as $work){
		if($work['Author'] == "" || is_null($work['Author'])) continue;
        if($category != "Southern Literature" && in_array_r($work['Author'], $southern)) continue;
		if(!in_array($work['Author'], $authors)){
			array_push($data, $work);
			array_push($authors, $work['Author']);
		}
	}
	shuffle($data);
	echo '<div class="owl-carousel">' . PHP_EOL;
	for ($x = 0; $x < min(7, count($data)); $x++) {
		$work = $data[$x];
		if ($work['ThumbnailImage'] == "" || $work['Title'] == "")
			continue;
		echo '<div class="carousel_item">' . PHP_EOL;
		echo '<a href="' . $authorPage . '?author=' . $work['Author'] . '">' . PHP_EOL;
		echo '<img src="Thumbnails/' . str_replace("\\", "/", $work['ThumbnailImage']) . '">' . PHP_EOL;
		echo '<div class="carousel_text">' . readyHTML(convertAuthorName($work['Author'])) . '</div>' . PHP_EOL;
		echo '</a>' . PHP_EOL;
		echo '</div>' . PHP_EOL;
	}
	echo '</div>' . PHP_EOL;
}

function error404($what, $showChildrenIcons = TRUE){
	$emotes = array(":(",":o",":O","D:",":c",":$","ಠ_ಠ", "(>_<)", "(?_?)", "(-_-)", "(~_~)", "(╯°□°）╯︵ ┻━┻", ":(", "ლ(ಠ益ಠლ)﻿");
	$error = "<div style='text-align:center;'> <br><h3>The <i>" . $what . "</i> you were looking for could not be found...<br><br>";
	$error .= $emotes[rand(0, count($emotes)) - 1];
	$error .= "<br><br>Please go back and try again!</p></h3> </div><br><br><br>";
	if($showChildrenIcons){
		$error .= "<table style='width:100%;'>
			<tr align='center'>
				<td><a href='children.php'><img src='images/section_icons/Children.png'></a></td>
				<td><a href='category.php?cat=Children/Rhymes'><img src='images/section_icons/Children!Rhymes.png'></a></td>
				<td><a href='category.php?cat=Children/Stories'><img src='images/section_icons/Children!Stories.png'></a></td>
				<td><a href='category.php?cat=Children/Rhymes and Stories'><img src='images/section_icons/Children!Rhymes_and_Stories.png'></a></td>
			<tr>
			<tr align='center' valign='top'>
				<td>Children's Section</td>
				<td>Rhymes</td>
				<td>Stories</td>
				<td>Rhymes and Stories</td>
			</tr>
		</table>";
	}
	echo $error;
}

function aboutHeader($currentPage){
	echo "<table class='header' style='width:100%;'>";
		echo "<tr align='center' valign='top'>";
			echo ($currentPage == "Introduction" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_LANDING.php><img src='images/section_icons/Children.png'><br><b>Introduction</b></a></td>";
			echo ($currentPage == "To Parents" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_parents.php><img src='images/about/parents.png'><br><b>To Parents</b></a></td>";
			echo ($currentPage == "To Teachers" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_teachers.php><img src='images/about/teachers.png'><br><b>To Teachers</b></a></td>";
			echo ($currentPage == "Why Classic Stories Matter" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_why.php><img src='images/about/why.png'><br><b>Why Classic Stories Matter</b></a></td>";
			echo ($currentPage == "History of the Project" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_history.php><img src='images/about/history.png'><br><b>History of the Project</b></a></td>";
			echo ($currentPage == "Resources" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_more.php><img src='images/about/more.png'><br><b>Resources</b></a></td>";
		echo "</tr>";
	echo "</table><br>";
}

function aboutHeaderSouthern($currentPage){
	echo "<table class='header' style='width:100%;'>";
		echo "<tr align='center' valign='top'>";
			echo ($currentPage == "Introduction" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_SL_intro.php><b>Introduction</b></a></td>";
			echo ($currentPage == "Voices" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_SL_voices.php><b>Voices</b></a></td>";
			echo ($currentPage == "Dialects" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_SL_dialects.php><b>Dialects</b></a></td>";
			echo ($currentPage == "Biographies" ? "<td class='selected'>" : "<td>");
				echo "<a href=ABOUT_SL_bios.php><b>Readers</b></a></td>";
		echo "</tr>";
	echo "</table>";
    
    //create author bar
    echo "<div style='width:100%; height: 70px; overflow:hidden; white-space:nowrap;'>";
    $catData = getAllInCategory("Southern Literature");
	$data = array();
	$authors = array();
	foreach($catData as $work){
		if($work['Author'] == "" || is_null($work['Author'])) continue;
		if(!in_array($work['Author'], $authors)){
			array_push($data, $work);
			array_push($authors, $work['Author']);
		}
	}
	shuffle($data);
    foreach($data as $authorWork) {
        echo "<a href='SOUTHERN_author.php?author=" . $authorWork['Author'] . "'>";
        echo '<img style="height:70px;" src="Thumbnails/' . $authorWork['ThumbnailImage'] . '">';
        echo "</a>";
    }
    echo "</div>";
    echo "<div style='width:100%; height: 3px; background-color:#808080'></div>";
    echo "<br>";
}

function adultOriginHeader($currentPage, $currentType){
	if($currentType != "") $currentType = ("&type=" . $currentType);
	echo "<table class='header' style='width:100%;'>";
		echo "<tr align='center' valign='top'>";
			echo ($currentPage == "All Origins" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?origin=All Origins" . $currentType . "'><img src='images/adult/all_origins.png'><br><b>All Origins</b></a></td>";
			echo ($currentPage == "American Literature" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?origin=American Literature" . $currentType . "'><img src='images/adult/america.png'><br><b>American</b></a></td>";
			echo ($currentPage == "British Literature" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?origin=British Literature" . $currentType . "'><img src='images/adult/uk.png'><br><b>British</b></a></td>";
			echo ($currentPage == "World Literature" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?origin=World Literature" . $currentType . "'><img src='images/adult/world.png'><br><b>World</b></a></td>";
		echo "</tr>";
	echo "</table>";
}

function adultTypeHeader($currentPage, $currentOrigin){
	if($currentOrigin != "") $currentOrigin = ("&origin=" . $currentOrigin);
	echo "<table class='header' id='bottom'>";
		echo "<tr align='center' valign='top'>";
			echo ($currentPage == "All Types" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?type=All Types" . $currentOrigin . "'><b>All Works</b></a></td>";
			echo ($currentPage == "Poetry" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?type=Poetry" . $currentOrigin . "'><b>Poetry</b></a></td>";
			echo ($currentPage == "Stories" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?type=Stories" . $currentOrigin . "'><b>Stories</b></a></td>";
			echo ($currentPage == "Books" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?type=Books" . $currentOrigin . "'><b>Books</b></a></td>";
			echo ($currentPage == "Nonfiction" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?type=Nonfiction" . $currentOrigin . "'><b>Nonfiction</b></a></td>";
			echo ($currentPage == "Plays" ? "<td class='selected'>" : "<td>");
				echo "<a href='ADULT_home.php?type=Plays" . $currentOrigin . "'><b>Plays</b></a></td>";
		echo "</tr>";
	echo "</table><br>";
}

function southernTypeHeader($currentPage){
	echo "<table class='header' id='bottom'>";
		echo "<tr align='center' valign='top'>";
			echo ($currentPage == "All Types" ? "<td class='selected'>" : "<td>");
				echo "<a href='SOUTHERN_home.php?type=All Types'><b>All Works</b></a></td>";
			echo ($currentPage == "Poetry" ? "<td class='selected'>" : "<td>");
				echo "<a href='SOUTHERN_home.php?type=Poetry'><b>Poetry</b></a></td>";
			echo ($currentPage == "Stories" ? "<td class='selected'>" : "<td>");
				echo "<a href='SOUTHERN_home.php?type=Stories'><b>Stories</b></a></td>";
			echo ($currentPage == "Books" ? "<td class='selected'>" : "<td>");
				echo "<a href='SOUTHERN_home.php?type=Books'><b>Books</b></a></td>";
			echo ($currentPage == "Nonfiction" ? "<td class='selected'>" : "<td>");
				echo "<a href='SOUTHERN_home.php?type=Nonfiction'><b>Nonfiction</b></a></td>";
		echo "</tr>";
	echo "</table><br>";
}

function convertAuthorName($name){
	if($name == "O. HENRY (W. S. Porter)") return "O. Henry (W. S. Porter)";
	$name = strtolower($name);
	if(strpos($name, ",") != 0){
		$exploded = explode(",", $name);
		$name = $exploded[1] . " " . $exploded[0];
	}
    if ($name[0] == " ") {
        $name = substr($name, 1);
    }
	return ucwords($name);
}

function cutDuplicates($works) {
    $noDuplicates = array();
    foreach($works as $work) {
        if(!in_array_r($work['Title'], $noDuplicates)) {
            array_push($noDuplicates, $work);
        }
    }
    return $noDuplicates;
}

//thanks internet
function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

?>
