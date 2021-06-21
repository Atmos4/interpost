<?php 

//Require
require '../include/db.php';
require '../assets/libs/Parsedown.php';

$articles = get_articles(9)->data;

$categories = get_all("categories")->data;

$Parsedown = new Parsedown();


header("Content-Type: application/rss+xml; charset=UTF-8");

$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
$rssfeed .= '<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">';
$rssfeed .= '<channel>';
$rssfeed .= '<title>Interpost</title>';
$rssfeed .= '<link>http://www.interpost.fr/feed/</link>';
$rssfeed .= '<description>Site d\'actualités sur la course d\orientation en France et dans le Monde</description>';
$rssfeed .= '<copyright>Copyright (C) 2019 INTERPOST</copyright>';

$rssfeed .= '<image>';
$rssfeed .= '<url>https://www.interpost.fr/images/favicon/favicon-32x32.png</url>';
$rssfeed .= '<title>Interpost</title>';
$rssfeed .= '<link>https://www.interpost.com</link>';
$rssfeed .= '<width>32</width>';
$rssfeed .= '<height>32</height>';
$rssfeed .= '</image>';

$rssfeed .= '<atom:link href="http://www.interpost.fr/feed/" rel="self" type="application/rss+xml"/>';

foreach ($articles as $article){

    $rssfeed .= '<item>';
    $rssfeed .= '<title><![CDATA[' . $article['title'] . ']]></title>';
    $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime($article['date'])) . '</pubDate>';
    $rssfeed .= '<description><![CDATA['.$article['subtitle'].']]></description>';
    $rssfeed .= '<enclosure  url="'.$article['image'].'" type="image/jpeg">'.'</enclosure>';
    $rssfeed .= '<guid isPermaLink="true">https://www.interpost.fr/article?id=' . $article['id'] . '</guid>';
    $rssfeed .= '<link>https://www.interpost.fr/article?id=' . $article['id'] . '</link>';
    $rssfeed .='<category>' . ($article['category_id']!=0?$categories[$article['category_id']]['name']:"News") . '</category>';
    $rssfeed .= '</item>';

}

$rssfeed .= '</channel>';
$rssfeed .= '</rss>';
echo $rssfeed;

