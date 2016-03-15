<?php
/** @var string $form */

$form = '<form action="" method="post"><p>' . $form . '<input id="submit" type="submit" value="Envoyer"/></p></form>';

//# Create new DOM object
//$domOb = new \DOMDocument();
//
//# Grab your HTML file
//$domOb->loadHTML($form);
//
//# Remove whitespace
//$domOb->preserveWhiteSpace = false;
//
//# Set the container tag
///** @var \DOMElement $container */
//$container = $domOb->getElementsByTagName('form')->item(0);
//$test = $container->getAttribute('method');

//$json = array('form' => [
//    'action' => '',
//    'method' => 'post',
//    'p' => [
//        'label' => [
//            'class' => 'required',
//            'for' => 'contenu',
//            ['Contenu']
//        ],
//        'textarea' => [
//            'name' => 'contenu',
//            'id' => 'contenu',
//            'cols' => '60'
//        ]
//    ]
//]);



?>

<?= $form ?>

