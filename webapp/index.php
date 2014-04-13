
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title>Down To Earth | Nasa Space App Challenge 2014</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-sortable.css">
    <!-- Custom styles for this template -->
    <link href="css/down.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->


    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>

    <script>            

            function success(position) {
              var geocoder;
              geocoder = new google.maps.Geocoder();
              var s = document.querySelector('#status');
              
              if (s.className == 'success') {
                // not sure why we're hitting this twice in FF, I think it's to do with a cached result coming back    
                return;
              }
              
              //s.innerHTML = "found you!";
              s.className = 'success';
              
              var mapcanvas = document.createElement('div');
              mapcanvas.id = 'mapcanvas';
              mapcanvas.style.height = '400px';
              mapcanvas.style.width = '560px';
                
              //document.querySelector('article').appendChild(mapcanvas);
              
              var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
              var city;
              geocoder.geocode({'latLng': latlng}, function(results, status) {
                  if (status == google.maps.GeocoderStatus.OK) {
                  console.log(results)
                    if (results[1]) {
                     //formatted address
                    //find country name
                    for (var i=0; i<results[0].address_components.length; i++) {
                        for (var b=0;b<results[0].address_components[i].types.length;b++) {

                        //there are different types that might hold a city admin_area_lvl_1 usually does in come cases looking for sublocality type will be more appropriate
                            if (results[0].address_components[i].types[b] == "locality") {
                                //this is the object you are looking for
                                city = results[0].address_components[i];
                                get_data(city.short_name);
                                s.innerHTML = city.short_name;
                                break;
                            }
                        }
                    }
                    //city data
                    //alert(city.short_name);


                    } else {
                      alert("No results found");
                    }
                  } else {
                    alert("Geocoder failed due to: " + status);
                  }
                });
              console.log(position.coords);

              /*
              // UNCOMMENT TO DEBUG
              var myOptions = {
                zoom: 15,
                center: latlng,
                mapTypeControl: false,
                navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
                mapTypeId: google.maps.MapTypeId.ROADMAP
              };
              var map = new google.maps.Map(document.getElementById("mapcanvas"), myOptions);
              
              var marker = new google.maps.Marker({
                  position: latlng, 
                  map: map, 
                  title:"You are here! (at least within a "+position.coords.accuracy+" meter radius)"
              });
              */
            }

            function error(msg) {
              var s = document.querySelector('#status');
              s.innerHTML = typeof msg == 'string' ? msg : "failed";
              s.className = 'fail';
              
              // console.log(arguments);
            }

            if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(success, error);
            } else {
              error('not supported');
            }

        </script>

         <script>
        function get_data(city){
            $.getJSON( "js/data.json", function( data ) {
                  var items = [];
                  items.push( "<thead><tr><th>Object</th><th data-defaultsort=\"desc\">Size(m) low</th><th>Size(m) high</th><th>Distance(km)</th></tr></thead>" );
                  items.push( "<tbody>" );
                  $.each( data, function( key, val ) {
                    var url = "<a href='?o="+val.object+"&h="+val.H+"&d="+val.distance + "'>"+val.object+'</a>';

                    var freebase = 'https://www.googleapis.com/freebase/v1/mqlread?query=[{  "name": null,  "/location/location/area": null,  "/architecture/structure/height_meters": null,  "/architecture/structure/height_meters<": '+val.H[0]+',  "/location/location/geolocation": {    "latitude": null,    "longitude": null  },  "/location/location/containedby|=": [    "'+city+'"  ],  "sort": "-/architecture/structure/height_meters",  "limit":1}]&cursor';
                    url = "<a href='"+freebase+"'>"+val.object+'</a>';
                    items.push( "<tr><td>" + url + "</td><td>"+ Math.round(val.H[0],2) +"</td><td>"+ Math.round(val.H[1],2) +"</td><td>"+ Math.round(val.distance,2) +"</td></tr>" );
                  });
                 items.push( "</tbody>" );
                 $("#data").html("");
                  $( "<table/>", {
                    "class": "table",
                    html: items.join( "" )
                  }).appendTo( "#data" ).addClass('sortable').addClass('table-striped');
                });
        }
        </script>

  </head>

  <body>

    <div class="blog-masthead">
      <div class="container">
        <nav class="blog-nav">
          <a class="blog-nav-item active" href="#">DOWN TO EARTH</a>
          <a class="blog-nav-item" href="#">ABOUT</a>

          <a class="blog-nav-item" style="float:right">Location: <span id="status">checking...</span></a>
        </nav>        
      </div>
    </div>

    <div class="container">

      <div class="blog-header">
        <img src="img/banner.jpg" />
      </div>

      <div class="row">

        <div class="col-sm-8 blog-main">

          <div class="blog-post">
            <h1>Near Earth Objects</h1>

            <div id="data"><i>loading data</i></div>            
            
          </div><!-- /.blog-post -->

        </div><!-- /.blog-main -->

        <div class="col-sm-3 col-sm-offset-1 blog-sidebar">
          <div class="sidebar-module sidebar-module-inset">
            <h4>About</h4>
            <p>Etiam porta <em>sem malesuada magna</em> mollis euismod. Cras mattis consectetur purus sit amet fermentum. Aenean lacinia bibendum nulla sed consectetur.</p>
          </div>
          <div class="sidebar-module">            
          <div class="sidebar-module">            
            <ol class="list-unstyled">
              <li><a href="#">GitHub</a></li>
              <li><a href="#">Twitter</a></li>
              <li><a href="#">Facebook</a></li>
            </ol>
          </div>
        </div><!-- /.blog-sidebar -->

      </div><!-- /.row -->

    </div><!-- /.container -->

    <div class="blog-footer">
      <p>Blog template built for <a href="http://getbootstrap.com">Bootstrap</a> by <a href="https://twitter.com/mdo">@mdo</a>.</p>
      <p>
        <a href="#">Back to top</a>
      </p>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src='js/moment.min.js'></script>
    <script src='js/bootstrap-sortable.js'></script>

  </body>
</html>
