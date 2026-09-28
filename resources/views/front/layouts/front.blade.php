<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <meta name="format-detection" content="telephone=no">
 @yield('meta_tags')

 <script type="application/ld+json">
 {"@context":"http:\/\/schema.org","@type":"Organization","address":{"@type":"PostalAddress","addressCountry":"UAE","addressLocality":"Dubai","addressRegion":"Dubai","streetAddress":"Suite 304, Cayan Business Centre, Tecom 3"},"email":"info@pkproperties.ae","image":"http:\/\/www.pkholidays.ae\/images\/logo.png","location":{"@type":"Place","address":{"@type":"PostalAddress","addressCountry":"UAE","addressLocality":"Dubai","addressRegion":"Dubai","streetAddress":"Suite 304, Cayan Business Centre, Tecom 3"}},"logo":"http:\/\/www.beontop.ae\/images\/logo.png","name":"PK Holidays","telephone":"+971 4 4541140","url":"http:\/\/www.pkholidays.ae"}
 </script>

  <script type="application/ld+json">
 {"@context":"http:\/\/schema.org","@type":"LocalBusiness","address":{"@type":"PostalAddress","addressCountry":"UAE","addressLocality":"Dubai","addressRegion":"Dubai","streetAddress":"Suite 304, Cayan Business Centre, Tecom 3"},"email":"info@pkproperties.ae","geo":{"@type":"GeoCoordinates","latitude":25.0964107,"longitude":55.1707},"image":"http:\/\/www.pkholidays.ae\/images\/logo.png","location":{"@type":"Place","address":{"@type":"PostalAddress","addressCountry":"UAE","addressLocality":"Dubai","addressRegion":"Dubai","streetAddress":"Suite 304, Cayan Business Centre, Tecom 3"}},"logo":"http:\/\/www.pkholidays.ae\/images\/logo.png","name":"PK Holidays","openingHours":["Mo,Tu,We,Th,Su 08:30-17:80"],"telephone":"+971 4 4 4541140","url":"http:\/\/www.pkholidays.ae"}
 </script>


<meta name="facebook-domain-verification" content="kl9dqb9dmhyry2fe5meespwn1aav8k" />
	  <!--Head ends-->
	 @include("partials._head")
	   <!--Head ends-->

	  <!-- Google tag (gtag.js) --> <script async src="https://www.googletagmanager.com/gtag/js?id=G-48WF0GKJ9R"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-48WF0GKJ9R'); </script>


  <!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '340143283816675');
  fbq('track', 'PageView');
</script>
<noscript>
  <img height="1" width="1" style="display:none" 
       src="https://www.facebook.com/tr?id=340143283816675&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
	  
<body>

@include("partials._nav")

<div class="page">

	@yield('content')
	 

</div>
    <!--Page Wrapper ends-->
  <!--Footer Starts-->
 @include("partials._footer")
  <!--Footer ends-->

    <!--Scripts starts-->
@include("partials._js")
     <!--Scripts ends-->

@yield('custom_js')

     
</body>

</html>