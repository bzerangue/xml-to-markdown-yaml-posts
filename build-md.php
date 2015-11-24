<?php
// USAGE: php convert.php


require_once __DIR__.'/vendor/autoload.php';

// Not available with this example
//require_once __DIR__.'/includes/address-geocoder-php/geocode.php';


// Edit the details below to your needs
$source_file = 'churches.xml';
$export_folder = 'churches'; // existing files will be over-written, use with care

if (file_exists($source_file)) {
  $xml = simplexml_load_file($source_file);
  $count = 0;
  foreach ($xml->{'churches-by-id'}->entry as $item) {
    $count ++;

    print "Exporting: (".$count.") " . $item->title."\n";

    //$path = $export_folder.substr($item->path,1);
    $path = $export_folder;

    if(!is_dir($path))
    {
        mkdir($path,0775);
    }

    //$name_of_file = 'index';

    $converter = new Markdownify\ConverterExtra;

    // META FIELDS

    $layout = 'churches';
    $title = $item->name; // Title
    $streetaddress = $item->{'street-address'}; // street-address
    $city = $item->city; // city
    $state = $item->{'state-abbr'}; // state
    $zipcode = $item->{'zip-code'}; // zip-code
    $phone = $item->{'main-phone'}; // main-phone
    //$addr = urlencode($streetaddress.' '.$city.', '.$state.' '.$zipcode);

    // define the address and set sensor to false
    // $geocodeintersection = array (
    // 'address' => urlencode($streetaddress.' '.$city.', '.$state.' '.$zipcode),
    // 'sensor'  => 'false'
    // );
    //
    // $georesult = getLatLng($geocodeintersection);
    // if ($georesult['status']) {
    // $geocode = $georesult['lat'] . ',' . $georesult['lon'];
    // }


    $website = $item->website; // website
    $mission = $item->{'mission-church'}; // mission-church
    $slug = slugify($title)."-".slugify($city);

    // $audio = $item->{'morning-sermon'}['url'];
    // $audiofile = $item->{'morning-sermon'}->filename;
    // $date = $item->date->date->start;



    $tags = array();
    $categories = array();

    $file_name = $path."/".$slug.".md";
    //$file_name = $path."/".$date."-".slugify($title).".md";
    //$file_name = $path."/".$name_of_file.".md";

    if ($title == '') {
        $title = 'Untitled post';
    }

    foreach($item->category as $taxonomy) {
        if ($taxonomy['domain'] == 'post_tag') {
            $tags[] = "'".$taxonomy['nicename']."'";
        }
        if ($taxonomy['domain'] == 'category') {
            $categories[] = "'".$taxonomy['nicename']."'";
        }
    }

    print "  -- filename: ".$file_name;


    $markdown  = "---\n";
    $markdown .= "layout: " . $layout ."\n";
    $markdown .= "title: " . $title ."\n";
    ($slug) ? $markdown .= "slug: " . $slug ."\n" : "";
    ($streetaddress) ? $markdown .= "address_street: " . $streetaddress ."\n" : "";
    ($city) ? $markdown .= "address_city: " . $city ."\n" : "";
    ($state) ? $markdown .= "address_state: " . $state ."\n" : "";
    ($zipcode) ? $markdown .= "address_zipcode: " . $zipcode ."\n" : "";
    // ($geocode) ? $markdown .= "address_geocode: " . $geocode ."\n" : "";
    ($phone) ? $markdown .= "phone: " . $phone ."\n" : "";
    ($website) ? $markdown .= "website: " . $website ."\n" : "";
    ($mission) ? $markdown .= "mission: " . $mission ."\n" : "";

    //$markdown .= "date: " . $date ."\n";
    //$markdown .= "speaker: " . $speaker ."\n";
    //($audiofile!=' ') ? $markdown .= "audio: " . $audio . " \n" : $markdown .= "scripture: \n";
    // foreach($item->mainsection->authors->author as $bookauthor) {
    //     $markdown .= "  - \n";
    //     $markdown .= "    name: " . $bookauthor->person->firstname . " " . $bookauthor->person->lastname . "\n";
    //     $markdown .= "    imgurl: " . $bookauthor->person->imageurl . "\n";
    // }
    // if ($genre) {
    //     $markdown .= "genres: \n";
    //    foreach($item->genres->genre as $bookgenre) {
    //         $markdown .= "  - \n";
    //         $markdown .= "    type: " . $bookgenre->displayname . "\n";
    //         $markdown .= "    handle: " . slugify($bookgenre->displayname) . "\n";
    //     }
    // }
    // if ($subject) {
    //     $markdown .= "subjects: \n";
    //    foreach($item->subjects->subject as $booksubject) {
    //         $markdown .= "  - \n";
    //         $markdown .= "    name: " . $booksubject->displayname . "\n";
    //         $markdown .= "    handle: " . slugify($booksubject->displayname) . "\n";
    //     }
    // }

    // $markdown .= "publisher: " . $publisher . "\n";
    // $markdown .= "publicationdate: " . $pub_date . "\n";
    // $markdown .= "isbn: " . $isbn . "\n";
    // $markdown .= "format: " . $format . "\n";
    // $markdown .= "coverimage: " . $coverimage . "\n";
    // $markdown .= "purchaseprice: " . $purchaseprice . "\n";
    // $markdown .= "coverprice: " . $coverprice . "\n";


    // if ($featuredvideo) {
    //    $markdown .= "featured_video: '" . $featuredvideo ."'\n";
    // }

    // if ($heroimg) {
    //    $markdown .= "hero_img: '" . $heroimg ."'\n";
    // }

    if (sizeof($tags)) {
      $markdown .= "tags: [".implode(", ", $tags)."]\n";
    }
    if (sizeof($categories)) {
      $markdown .= "categories: [".implode(", ", $categories)."]\n";
    }

    // $markdown .= "quantity: " . $quantity . "\n";
    $markdown .= "---\n\n";
    //$markdown .= $item->children('content', true)->encoded;
    // $markdown .= $plot;
    $markdown .= "\n";

    file_put_contents($file_name, $markdown);

    print "\n";
  }
}

// Credit: http://sourcecookbook.com/en/recipes/8/function-to-slugify-strings-in-php
function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    if (function_exists('iconv'))
    {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text))
    {
        return 'n-a';
    }

    return $text;
}
