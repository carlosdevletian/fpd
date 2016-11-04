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
        <div id="fpd">
            <div class="fpd-product" title="Titulo" data-thumbnail="http://bit.ly/2fiDvEl">
                
            </div>
        </div>

        <button id="crear">
            CREAR IMAGEN
        </button>

        <image src="{{ route('image') }}"></image>


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
                    stageWidth: 1200, 
                    stageHeight: 800, 
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
                        boundingBox: "Base"
                    },
                    customImageParameters: {
                        draggable: true,
                        removable: true,
                        resizable: true,
                        rotatable: true,
                        colors: '#000',
                        autoCenter: true,
                        boundingBox: "Base"
                    },
                };

                var yourDesigner = new FancyProductDesigner($fpd, pluginOpts);

                //create an image
                $('#crear').click(function(){

                    yourDesigner.getProductDataURL(function(dataURL) {
                        $.post("save", { base64_image:  dataURL}, function(data) {
                            if(data) {
                                data.epa;
                            }
                            else {
                                // console.log('super peo');
                            }
                        });    
                    });



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
