<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dropzone</title>

        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}">

        <style>
            #dropzone {
              height: 130px;
              border: 5px dashed;
              margin: 1em;
              text-align: center;
              cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content card-body card-block">
                <div id="dropzone">
                  <br><br><span>Drop your folders here.</span>
                  <input id="clickablefileinput" type="file" style="display:none" webkitdirectory />
                </div>
            </div>
        </div>

        <script>
            var dropzone = document.getElementById("dropzone");
            var dropzoneFallback = document.getElementById("clickablefileinput");

            dropzone.addEventListener("click", function(e) {
                dropzoneFallback.click();
            });

            dropzoneFallback.addEventListener("change", function(event) {
                var items = event.target.files;
                for (var i = 0; i < items.length; i++) {
                    sendfile(items[i].webkitRelativePath, items[i]);
                }
            }, false);

            dropzone.addEventListener("drop", function(e) {
              e.stopPropagation();
              e.preventDefault();

              var items = event.dataTransfer.items;
              for (var i = 0; i < items.length; i++) {
                var entry = items[i].webkitGetAsEntry();
                if (entry) {
                  traverse(entry);
                }
              }
            }, false);

            dropzone.ondragover = function (e) {
              e.preventDefault()
            }

            function traverse(entry, path) {
              path = path || "";
              if (entry.isFile) {
                // Get file
                entry.file(function(file) {
                    sendfile(path + file.name, file);
                });
              } else if (entry.isDirectory) {
                // Get folder contents
                var dirReader = entry.createReader();
                dirReader.readEntries(function(entries) {
                  for (var i = 0; i < entries.length; i++) {
                    traverse(entries[i], path + entry.name + "/");
                  }
                });
              }
            }

            sendfile = function (path, file) {
                var formData = new FormData();
                var request = new XMLHttpRequest();

                formData.set('path', path);
                formData.set('file', file);
                formData.set('_token', '{{ csrf_token() }}')
                request.open('POST', '/store');
                request.send(formData);
            }
        </script>
    </body>
</html>
