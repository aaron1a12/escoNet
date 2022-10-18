<?php

class customPage extends page {
    public $title = 'WebGL';
    
    function head() {?>
<style>
  #renderCanvas {
    width: 100%;
    height: 100%;
    touch-action: none;
  }
</style>
<script src="/_inc/js/babylon.1.14-beta.js"></script>
<script src="/_inc/js/hand-1.3.8.js"></script>
<script src="/_inc/js/cannon.js"></script>  <!-- optional physics engine -->

<?php }
    function content() {
?>

       <h1>BabylonJS (WebGL Engine)</h1>

        <canvas id="renderCanvas" height="450" width="400"></canvas>

        <script>
            
        // Get the canvas element from our HTML above
        var canvas = document.getElementById("renderCanvas");

        // Load the BABYLON 3D engine
        var engine = new BABYLON.Engine(canvas, true);

        // This begins the creation of a function that we will 'call' just after it's built
        var createScene = function () {

            // Now create a basic Babylon Scene object 
            var scene = new BABYLON.Scene(engine);

            // This creates and positions a free camera
            var camera = new BABYLON.FreeCamera("camera1", new BABYLON.Vector3(0, 5, -10), scene);

            // This targets the camera to scene origin
            camera.setTarget(new BABYLON.Vector3.Zero());

            // This attaches the camera to the canvas
            camera.attachControl(canvas, false);

            // This creates a light, aiming 0,1,0 - to the sky.
            var light = new BABYLON.HemisphericLight("light1", new BABYLON.Vector3(0, 1, 0), scene);

            // Dim the light a small amount
            light.intensity = 0.5;

            // Let's try our built-in 'sphere' shape. Params: name, subdivisions, size, scene
            var sphere = BABYLON.Mesh.CreateSphere("sphere1", 16, 2, scene);

            // Move the sphere upward 1/2 its height
            sphere.position.y = 1;

            // Let's try our built-in 'ground' shape.  Params: name, width, depth, subdivisions, scene
            var ground = BABYLON.Mesh.CreateGround("ground1", 6, 6, 2, scene);

            // Leave this function
            return scene;

        };  // End of createScene function


        // Now, call the createScene function that you just finished creating
        var scene = createScene();

        // Register a render loop to repeatedly render the scene
        engine.runRenderLoop(function () {
        scene.render();
        });
            
            
        </script> 
<?php
    }
}

new customPage();