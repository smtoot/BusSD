<?php
header("Content-type: text/css; charset: UTF-8");
$color = $_GET['color'];
$secondColor = $_GET['secondColor'];

function checkhexcolor($c)
{
    return preg_match('/^[a-f0-9]{6}$/i', $c);
}

if (isset($_GET['color']) && !empty($_GET['color']) && checkhexcolor($_GET['color'])) {
    $color = '#' . $_GET['color'];
}

if (!$color) {
    $color = "#faa603";
}

if (isset($_GET['secondColor']) && !empty($_GET['secondColor']) && checkhexcolor($_GET['secondColor'])) {
    $secondColor = '#' . $_GET['secondColor'];
}

if (!$secondColor) {
    $secondColor = "#faa603";
}
?>



.cmn-btn, .feature-section .feature-item .feature-icon i, .feature-section .feature-item.active .feature-icon i, .title-border::before, .title-border::after, .pricing-section .pricing-item:hover .pricing-header .sub-title span, .pricing-section .pricing-item.active .pricing-header .sub-title span, .scrollToTop, .feature-item::before, .process-icon, .process-area::before {
background-color: <?php echo $color ?> !important;
}

.choose-section .choose-item:hover .choose-content .title, .choose-section .choose-item.active .choose-content .title{
border-color: <?php echo $secondColor ?> !important;
}


.text-color-1, .header-bottom-area .navbar-collapse .main-menu li a:hover, .header-bottom-area .navbar-collapse .main-menu li a.active, .section-title span, .process-section .process-item .process-devider::after, .feature-section .feature-item:hover .feature-content .title, .pricing-section .pricing-item .pricing-body .pricing-list li i, .footer-widget ul li i, .navbar-toggler span, .text--base, .cookies-card__content a{
color: <?php echo $color ?> !important;
}



.pricing-section .pricing-item .pricing-header .sub-title span, .preloader, .call-to-action-section, .feature-item, .cmn-btn-active, .choose-section .choose-item .choose-icon i, .header-top-area, .bg-overlay-primary:before, .bg-overlay-primary-two:before, .privacy-area {
background-color: <?php echo $secondColor ?>;
}

.cmn-btn:focus, .cmn-btn:hover {
box-shadow: 0 0 20px <?php echo $color ?>99 !important;
}

::selection {
background-color: <?php echo $color ?> !important;
color: white;
}

.feature-section .feature-item:hover .feature-icon i, .feature-section .feature-item.active .feature-icon i {
background-color: <?php echo $color ?>;
color: white !important;
}

.process-section .process-item .process-devider {
background-image: linear-gradient(90deg, <?php echo $color ?>, <?php echo $color ?> 40%, transparent 40%, transparent 100%);
}

.pricing-section .pricing-item::before, .pricing-section .pricing-item::after {
background-color: <?php echo $color ?>1a;
}

*::-webkit-scrollbar-button, *::-webkit-scrollbar-thumb {
background-color: <?php echo $color ?>;
}

.client-section .client-content .client-icon i {
color: <?php echo $color ?>33;
}


.footer-social li a:hover, .footer-social li a.active {
background-color: <?php echo $color ?>;
}
.footer-social li a:hover i, .footer-social li a.active i {
color: #fff !important;
}

.header-bottom-area {
background-color: <?php echo $secondColor ?>99;
}

.cookies-card__icon {
background: <?php echo $color ?> !important;
}


.dropdown-lang .dropdown-menu li:hover {
color: #fff !important;
background-color: <?php echo $color ?> !important;
}