<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" href="{{ URL::to('css/FancyProductDesigner-all.min.css') }}">

         <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>

        <title>FPD</title>
    </head>
    <body>
        <div id="fpd" class="fpd-container fpd-topbar fpd-hidden-tablets">
            <div class="fpd-product" title="Titulo" data-thumbnail="http://bit.ly/2fiDvEl">
                <img src= "{{ URL::to('images/pulsera.png') }}"
                     title="Pulsera"
                     data-parameters=
                        '{"left": 340, 
                        "top": 329, 
                        "draggable": true,
                        "removable": false,
                        "autoCenter": true,
                        "zChangeable": false,
                        "colors": "#ffffff,#e3e3e3,#000000,#ffff80,#ff6666,#00ff80",
                        "z": 2 
                        }'
                />
                <!-- <img src= "{{ URL::to('images/Backdrop.png') }}"
                     title="Pulsera"
                     data-parameters=
                        '{"draggable": false,
                        "removable": false,
                        "autoCenter": true,
                        "zChangeable": false,
                        "z": 1 
                        }'
                /> -->
                <span title="Any Text" 
                      data-parameters=
                        '{"boundingBox": "Pulsera", 
                        "removable": true, 
                        "draggable": true, 
                        "rotatable": true, 
                        "resizable": true, 
                        "outOfBoundaryColor": "#FFFF00",
                        "autocenter": true,
                        "z": -1,
                        "colors": "#000000"}'
                ></span>
            </div>

            <div class="fpd-design">
                    <!-- CATEGORÍA 1 PARA LOS DISEÑOS -->
                    <div class="fpd-category" title="Icons" data-thumbnail="http://bit.ly/2e6t2Ow"> 
                    @foreach($images as $image)
                        <img src="{{ $image }}" title="Agenda" data-parameters='{"zChangeable": true, "left": 215, "top": 200, "colors": "#000000", "removable": true, "draggable": true, "rotatable": true, "resizable": true, "boundingBox": "Pulsera", "autoCenter": true}' />
                    @endforeach
                    </div>
            </div>

        </div>

        <button id="crear">
            CREAR IMAGEN
        </button>

       <script
            src="https://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
            crossorigin="anonymous">
        </script>
        <script
            src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
            integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
            crossorigin="anonymous">
        </script>
        <!-- HTML5 canvas library -->
        <script src="{{ URL::to('js/fabric.min.js') }}" type="text/javascript"></script>
        <!-- The plugin itself -->
        <script src="{{ URL::to('js/FancyProductDesigner-all.min.js') }}" type="text/javascript"></script>
        <script type="text/javascript">
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).ready(function(){

                var $fpd = $('#fpd'),
                pluginOpts = {
                    stageWidth: 900, 
                    stageHeight: 500, 
                    langJSON: false,
                    actions:  {
                        'top': ['download', 'snap', 'preview-lightbox'],
                        'right': ['magnify-glass', 'zoom', 'reset-product'],
                        'bottom': ['undo','redo'],
                        'left': ['manage-layers','save']
                    },
                    customTextParameters: {
                        colors: "#000000,#ffffff",
                        removable: true,
                        resizable: true,
                        draggable: true,
                        rotatable: true,
                        autoCenter: true,
                        boundingBox: "Pulsera",
                        toolbarPlacement: "inside-top",
                        colors: "#e3e3e3,#000000,#ffff80,#ff6666,#00ff80",
                    },
                    customImageParameters: {
                        draggable: true,
                        removable: true,
                        resizable: true,
                        rotatable: true,
                        autoCenter: true,
                        boundingBox: "Pulsera",
                        z: -1,
                    },
                    outOfBoundaryColor: "#FF0000",
                    toolbarPlacement: "inside-top",
                    fonts: ['Arial', 'Helvetica', 'OperatorMono-Medium'],
                };

                var yourDesigner = new FancyProductDesigner($fpd, pluginOpts);

                //create an image
                $('#crear').click(function(){

                    yourDesigner.getProductDataURL(function(dataURL) {
                        $.post("save", { base64_image:  dataURL}, function(data) {
                            if(data) {
                                console.log(data.message);
                            }
                            else {
                                // console.log('super peo');
                            }
                        });    
                    }, 'transparent');

                    var productViews = yourDesigner.getProduct();
                    //loop through all views
                    for(var i=0; i < productViews.length; ++i) {
                        //output all single view objects
                        console.log(productViews[i]);
                    }


                    var image = yourDesigner.createImage(false, false, 'transparent',{ format : 'pdf' });
                    // console.log('Se creó la imagen');
                });

                //api methods can be used
                yourDesigner.print()

                //you can listen to events
                $fpd.on('productCreate', function() {
                //do something
                });
            });
        </script>
    </body>
</html>
