<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            box-sizing: border-box;
        }

        img {
            width: 50px;
            height: 50px;
        }

        div {
            width: 100px;
            height: 100px;
            border: 1px solid black;
            text-align: center;
            margin: 10px;
            padding: 20px;
        }
    </style>
</head>

<body>

    <div id="div2">
        <img src="test.jpg" alt="test.jpg" draggable="true" id="draggableImage">
    </div>
    <div id="div1">

    </div>
    <script>
        const draggableImage = document.getElementById('draggableImage');
        draggableImage.addEventListener('dragstart', function(event) {
            event.dataTransfer.setData('text', event.target.id);
        });
        const div1 = document.getElementById('div1');
        div1.addEventListener('dragover', function(event) {
            event.preventDefault();
        });

        div1.addEventListener('drop', function(event) {
            event.preventDefault();
            const imgId = document.getElementById(event.dataTransfer.getData('text'));
            event.target.appendChild(imgId);
        });

        const div2 = document.getElementById('div2');
        div2.addEventListener('dragover', function(event) {
            event.preventDefault();
        });

        div2.addEventListener('drop', function(event) {
            event.preventDefault();
            const imgId = document.getElementById(event.dataTransfer.getData('text'));
            event.target.appendChild(imgId);
        });
    </script>

</body>

</html>