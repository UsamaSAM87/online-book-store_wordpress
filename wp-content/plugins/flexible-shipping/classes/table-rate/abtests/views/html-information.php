<?php
/**
 * @var $support_link string
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><h4><strong><?php
	echo sprintf(
		__(
			'Do you have a problem with our plugin or do not know how to configure it? Write to our support department - we will try to help. %1$sGo to contact form →%2$s',
			'flexible-shipping'
		),
		"<a target=\"_blank\" href=\"{$support_link}\">",
		'</a>'
	);
?></strong></h4>
