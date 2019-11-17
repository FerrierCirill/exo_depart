<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title> 
    <style>
        .icon {
            height : 1em;
            width  : 1em;
        }

        h1 {
            color : red;
            fill: currentColor;
        }

        h1:hover {
            color : orange;
        }

        h4 {
            color : orange;
            fill: currentColor;
        }

        h4:hover {
            color : blue;
            stroke : currentColor;
            stroke-width="5px";
        }

        p {
            color : blue;
            fill: currentColor;
        }

        p:hover {
            color : red;
        }
    </style>
</head>
<body>
    
    <?php include 'svg/sprite.svg' ?>

    <h1>
        <svg class="icon">
            <use xlink:href="#t1"></use>
        </svg>
        Fleur
    </h1>

    <h4>
        <svg class="icon">
            <use xlink:href="#t2"></use>
        </svg>
        Montage
    </h4>

    <p>
        <svg class="icon">
            <use xlink:href="#t3"></use> 
        </svg>
         Logo
    </p>
</body>
</html>