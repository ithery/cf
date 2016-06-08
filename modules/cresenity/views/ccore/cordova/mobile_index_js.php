<script>
//@author ROOTad @since this file created
var app = {
    localManifest: '',
    localUpdateManifest: '',
    firstRun: '0',
    errorNumber: '0',
    errorDownload: '0',
    senderID: '736679606572',
    registrationId: '',
    URL: '<?php echo $url; ?>',
    fileProtocol: 'cdvfile://',
    localProtocol: 'file:///',
    localAndroidFileLocation: 'storage/',
    filePersistent: 'localhost/persistent/',
    androidFileLocation: 'Android/data',
    fileLocation: '<?php echo $id_unique; ?>/files',
    manifestFileServer: '<?php echo $manifest_file_server; ?>',
    manifestFileUpdateServer: '<?php echo $manifest_file_server; ?>',
    // Application Constructor
    initialize: function() {
        this.bindEvents();
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicitly call 'app.receivedEvent(...);'
    onDeviceReady: function() {
        app.receivedEvent('deviceready');
        document.addEventListener("backbutton", function() {
        }, false);
        document.addEventListener("offline", function() {
            var deleteFile = 'manifestupdate.json';
            if(app.firstRun == 1) {
                deleteFile = 'manifest.json';
            }
            window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function(fileSystem) {
                fileSystem.root.getFile(app.androidFileLocation + '/' + app.fileLocation + '/' + deleteFile, {create:false}, function(fileEntry) {
                    fileEntry.remove(function(file){
                        console.log("File removed!");
                    },function(error){
                        console.log("error deleting the file ");
                        console.log(error);
                        });
                    },function(){
                        console.log("file does not exist 1");
                    });
                },function(evt){
                    console.log(evt.target.error.code);
            });
            document.getElementById("message_manifest").className = "";
            document.getElementById("message_manifest").className = "event error_message";
            document.getElementById('message_manifest').innerHTML = 'NO INTERNET ACCESS AVAILABLE';
        }, false);
        document.addEventListener("online", function() {
            app.getSplashScreen();
            document.getElementById("message_manifest").className = "";
            document.getElementById("message_manifest").className = "event listening";
            document.getElementById('message_manifest').innerHTML = 'WELCOME';
        }, false);
        // window.BOOTSTRAP_OK = true;
    },
    gotLocalManifest: function(fileEntry) {
        console.log('this gotLocalManifest - ' + app.firstRun);
        // app.getSplashScreen();
        fileEntry.file(function(file) {
            var reader = new FileReader();

            reader.onloadend = function(e) {
                var error = 0;
                // console.log(this.result);
                try {
                    console.log(this);
                    app.localManifest = JSON.parse(this.result);
                } catch(e) {
                    console.log('ERROR LOAD FILE LOCAL MANIFEST');
                    console.log(e);
                    error++;
                    app.errorNumber++;
                    if(app.errorNumber > 3) {
                        document.getElementById("message_manifest").className = "";
                        document.getElementById("message_manifest").className = "event error_message";
                        document.getElementById('message_manifest').innerHTML = 'CLOUD ERROR';
                    } else {
                        app.getManifestFromServer('', '');
                    }
                //     error++;
                }
                if(error == 0) {
                    document.getElementById('message_manifest').innerHTML = 'LOAD DATA DONE';
                    var last_file = '';
                    var file_location = '';
                    if(app.firstRun == 0) {
                        app.getUpdateManifestFromServer();
                    } else {
                        var files =  app.localManifest.files;
                        var z = 0;
                        var fileNumber = 0;
                        for (var key in files) {
                            fileNumber++;
                        }
                        app.downloadBulkFile(files, 0, fileNumber, false);
                    }
                }
            }
            reader.readAsText(file);
        });
    },
    notFoundLocalManifest: function(e) {
        // console.log(e);
        console.log('DIE 3');
        app.firstRun = 1;
        
        app.getManifestFromServer('', '');
    },
    loadLocalManifest: function() {
        if(app.firstRun == 1) {
           // app.getSplashScreen(); 
        } 
       var fileURL = app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + '/' + "manifest.json";
       document.getElementById('message_manifest').innerHTML = 'PLEASE WAIT, LOAD DATA';
       window.resolveLocalFileSystemURL(fileURL, app.gotLocalManifest, app.notFoundLocalManifest);

    },
    getManifestFromServer: function (url, fileURL) {
        var fileTransfer = new FileTransfer();
        var uri = encodeURI(app.manifestFileServer);
        var fileURL = app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + '/' + "manifest.json";
        document.getElementById('message_manifest').innerHTML = 'DOWNLOAD DATA FROM CLOUD';
        fileTransfer.download(
            uri,
            fileURL,
            function(entry) {
                console.log(entry);
                console.log('DOWNLOAD DONE');
                app.loadLocalManifest();
            },
            function(error) {
                console.log("download error source " + error.source);
                console.log("download error target " + error.target);
                console.log("upload error code" + error.code);
                document.getElementById("message_manifest").className = "";
                document.getElementById("message_manifest").className = "event error_message";
                document.getElementById('message_manifest').innerHTML = 'ERROR CONNECTING TO CLOUD FAILED';
            },
            false,
            {
                headers: {
                    "Authorization": "Basic dGVzdHVzZXJuYW1lOnRlc3RwYXNzd29yZA==",
                    "Access-Control-Allow-Origin": "62hall.com"
                }
            }
        );
    },

    diffManifest: function () {
        console.log("check manifest");
        //check both manifest version
        // if(app.localUpdateManifest.version != app.localManifest.version) {
            var files =  app.localManifest.files;
            var filesUpdate =  app.localUpdateManifest.files;
            var z = 0;
            var fileNumber = 0;
            var last_file = '';
            var file_location = '';
            for (var key in filesUpdate) {
                fileNumber++;
            }
            app.downloadFile(files, filesUpdate, 0, fileNumber, false);
            
        // } else {
        //     app.afterDownload(1);
        // }
    },
    loadHtml: function () {
        resolveLocalFileSystemURL(app.fileProtocol + app.filePersistent + app.androidFileLocation, function(entry) {
            last_file = entry.toURL();
            last_file = last_file.split("/");;
            file_location = '';
            console.log('last_file = ' + last_file);
            for (var key in last_file) {
                if(last_file[key] == 'file') {
                    file_location += last_file[key] + '///';
                } else if(last_file[key] == 'Android') {
                    break;
                } else {
                    file_location += last_file[key] + '/';
                }
            }
            jQuery.ajax({
                type: 'post',
                url: app.URL + 'home/init',
                dataType: 'html',
                async : false,
                data: {
                    url: file_location + app.androidFileLocation + '/' + app.fileLocation,
                    registration_id: app.registrationId,
                    sender_id: app.senderID,
                    available: device.available,
                    cordova: device.cordova,
                    isVirtual: device.isVirtual,
                    manufacturer: device.manufacturer,
                    model: device.model,
                    platform: device.platform,
                    serial: device.serial,
                    uuid: device.uuid,
                    version: device.version
                }
            }).done(function( data ) {
                console.log(data);
                app.getPage();
            }).fail(function( jqXHR, textStatus ) {
                console.log( "Request page failed: " + textStatus );
                document.getElementById("message_manifest").className = "";
                document.getElementById("message_manifest").className = "event error_message";
                document.getElementById('message_manifest').innerHTML = 'ERROR CANNOT DOWNLOAD DATA';
            });
            setTimeout(function() {
                // document.getElementById("splash_screen").style.display= "none";
                
            }, 2000);
        });
    },
    renameFile: function () {
        window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function(fileSystem){
            fileSystem.root.getFile(app.androidFileLocation + '/' + app.fileLocation + '/' + 'manifestupdate.json', null, function(fileEntry){
                fileSystem.root.getDirectory(app.androidFileLocation + '/' + app.fileLocation, {create: true}, function (dirEntry) {
                    parentEntry = new DirectoryEntry('manifestupdate.json', app.androidFileLocation + '/' + app.fileLocation + '/');
                    fileEntry.moveTo(dirEntry, 'manifest.json', function () {
                        console.log("File Renamed!");
                        app.loadHtml();
                    },function(){
                        console.log("error rename the file " + error.code);
                        });
                    },function(){
                        console.log("file does not exist 2");
                    });
                },function(evt){
                    console.log(evt.target.error.code);
                });
               
            },function(evt){
                console.log(evt.target.error.code);
        });
    },
    afterDownload: function (status) {
        if(status == 1) {
            // app.renameFile();
            console.log('try remove' + app.androidFileLocation + '/' + app.fileLocation + '/' + 'manifest.json');
            window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function(fileSystem) {
                fileSystem.root.getFile(app.androidFileLocation + '/' + app.fileLocation + '/' + 'manifest.json', {create:false}, function(fileEntry) {
                    fileEntry.remove(function(file){
                        console.log("File removed!");
                        app.renameFile();
                    },function(error){
                        console.log("error deleting the file ");
                        console.log(error);
                        });
                    },function(){
                        console.log("file does not exist 1");
                    });
                    
                },function(evt){
                    console.log(evt.target.error.code);
            });
        } else {
            app.loadHtml();
        }
        console.log('AFTER DOWNLOAD - ' + status);
        
    },
    getPage: function () {
        document.getElementById('message_manifest').innerHTML = 'Preparing, Please Wait';
        resolveLocalFileSystemURL(app.fileProtocol + app.filePersistent + app.androidFileLocation, function(entry) {
            last_file = entry.toURL();
            last_file = last_file.split("/");;
            file_location = '';
            console.log('last_file = ' + last_file);
            for (var key in last_file) {
                if(last_file[key] == 'file') {
                    file_location += last_file[key] + '///';
                } else if(last_file[key] == 'Android') {
                    break;
                } else {
                    file_location += last_file[key] + '/';
                }
            }
            console.log('loop file_location1' + file_location);
            console.log('AFTER DOWNLOAD');
            jQuery.ajax({
                type: 'get',
                url: app.URL + 'home',
                dataType: 'html',
                async : false,
                data: {
                    url: file_location + app.androidFileLocation + '/' + app.fileLocation,
                    registration_id: app.registrationId,
                    sender_id: app.senderID,
                    available: device.available,
                    cordova: device.cordova,
                    isVirtual: device.isVirtual,
                    manufacturer: device.manufacturer,
                    model: device.model,
                    platform: device.platform,
                    serial: device.serial,
                    uuid: device.uuid,
                    version: device.version
                }
            }).done(function( data ) {
                window.resolveLocalFileSystemURL(cordova.file.dataDirectory, function (fileSystem) {
                    var reader = fileSystem.createReader();
                    reader.readEntries(
                        function (entries) {
                            for(var now in entries) {
                                entries[now].remove(function(file){
                                }, function(){
                                });
                            }
                        }, function (err) {
                            console.log(err);
                        }
                    );
                }, function (err) {
                    console.log(err);
                });
                app.redirectPage(data);
            }).fail(function( jqXHR, textStatus ) {
                console.log( "Request page failed: " + textStatus );
                document.getElementById("message_manifest").className = "";
                document.getElementById("message_manifest").className = "event error_message";
                document.getElementById('message_manifest').innerHTML = 'ERROR CANNOT DOWNLOAD DATA';
            });
            setTimeout(function() {
                // document.getElementById("splash_screen").style.display= "none";
                
            }, 2000);
        });
    },
    redirectPage: function(data){
        var today = new Date();
        var date = today.getDay() + today.getDate() + today.getFullYear() + today.getHours() + today.getMinutes() + today.getMilliseconds();
        window.resolveLocalFileSystemURL(cordova.file.dataDirectory, function(dir) {
            console.log("got main dir",dir);
            dir.getFile(date + "home.html", {create:true}, function(file) {
                console.log("got the file", file);
                logOb = file;
                if(!logOb) return;
                logOb.createWriter(function(fileWriter) {
                    
                    fileWriter.seek(fileWriter.length);
                    
                    var blob = new Blob([data], {type:'text/html'});
                    fileWriter.write(blob);
                    fileWriter.onwrite = function(evt) {
                        console.log("write success");
                        window.location.href= cordova.file.dataDirectory + date + "home.html";
                    };
                    console.log("ok, in theory i worked");
                }, function() {
                    console.log( "Request page failed: " + textStatus );
                    document.getElementById("message_manifest").className = "";
                    document.getElementById("message_manifest").className = "event error_message";
                    document.getElementById('message_manifest').innerHTML = 'ERROR CANNOT DOWNLOAD DATA';
                });          
            });
        });
    },
    downloadFile: function (option, optionUpdate, number, fileNumber, splashscreen) {
        document.getElementById('message_manifest').innerHTML = 'CHECK UPDATE ' + number + ' of ' + fileNumber;
        console.log('splashscreen1' + splashscreen);
        var fileUpdate = optionUpdate[number];
        var downloadStatus = false;
        var file;
        var notFound = true;
        var fileURL = app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + fileUpdate.file;

        notFound = false;
        window.resolveLocalFileSystemURL(fileURL, function() {
            console.log('file FOUND' + fileURL);
            for (var key in option) {
                file = option[key];
                if(file.file == fileUpdate.file) {
                    console.log('DEBUG AAA');
                    if(file.version != fileUpdate.version) {
                        downloadStatus = true;
                    }
                    break;
                }
            }
            if(downloadStatus) {
                var fileTransfer = new FileTransfer();
                var url_file = fileUpdate.url;
                if (url_file.charAt(0) == '/') {
                    url_file = url_file.replace('/', '');
                }
                var uri = encodeURI(app.URL + url_file);
                // console.log("downloadFrom " + option.split("/"));
                // console.log("downloadFile " + app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + option.file);
                var serverMainfest = '';
                // document.getElementById('message_manifest').innerHTML = 'CONNECTING TO CLOUD';
                fileTransfer.download(
                    uri,
                    fileURL,
                    function(entry) {
                        // window.resolveLocalFileSystemURL(fileURL, app.loadUpdateManifest, app.notFoundUpdateManifest);
                        number++;
                        if(fileNumber > number) {
                            app.downloadFile(option, optionUpdate, number, fileNumber, splashscreen);

                        } else {
                            app.afterDownload(1);
                        }
                    },
                    function(error) {
                        console.log("download error source " + error.source);
                        console.log("download error target " + error.target);
                        console.log("upload error code" + error.code);
                        document.getElementById("message_manifest").className = "";
                        document.getElementById("message_manifest").className = "event error_message";
                        document.getElementById('message_manifest').innerHTML = 'ERROR CANNOT DOWNLOAD DATA';
                        app.errorDownload++;
                    },
                    false,
                    {
                        headers: {
                            "Authorization": "Basic dGVzdHVzZXJuYW1lOnRlc3RwYXNzd29yZA=="
                        }
                    }
                );
            } else {
                number++;
                if(fileNumber > number) {
                    app.downloadFile(option, optionUpdate, number, fileNumber, splashscreen);

                } else {
                    app.afterDownload(1);
                }
            }
        }, function() {
            console.log('File not FOUND' + fileURL);
            var download_url = fileUpdate.url;
            console.log('aaaa1 ' + download_url);
            console.log('aaaa1 ' + fileURL);
            var download_url_array = download_url.split("/");
            var lenght_array = 0;
            for (var key in download_url_array) {
                lenght_array++;
            }
            lenght_array--;
            download_url = '';
            for (var key in download_url_array) {
                if(key < lenght_array) {
                    download_url += download_url_array[key] + '/';
                }
            }
            console.log('aaaa1 ' + download_url);
            var create_folder_location = app.androidFileLocation + '/' + app.fileLocation + download_url + '/';
            // var file_folder = "" + app.androidFixleLocation + '/' + app.fileLocation + option.file;
            var dirManager = new DirManager();
            dirManager.create_r(create_folder_location, function() {
                var fileTransfer = new FileTransfer();
                var url_file = fileUpdate.url;
                console.log('aaaa1 ' + url_file.charAt(0));
                if (url_file.charAt(0) == '/') {
                    url_file = url_file.replace('/', '');
                }
                console.log('aaaa1 ' + url_file);
                var uri = encodeURI(app.URL + url_file);
                // console.log("downloadFrom " + option.split("/"));
                // console.log("downloadFile " + app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + option.file);
                var serverMainfest = '';
                // document.getElementById('message_manifest').innerHTML = 'CONNECTING TO CLOUD';
                fileTransfer.download(
                    uri,
                    fileURL,
                    function(entry) {
                        // window.resolveLocalFileSystemURL(fileURL, app.loadUpdateManifest, app.notFoundUpdateManifest);
                        number++;
                        if(fileNumber > number) {
                            app.downloadFile(option, optionUpdate, number, fileNumber, splashscreen);

                        } else {
                            app.afterDownload(1);
                        }
                    },
                    function(error) {
                        console.log("download error source " + error.source);
                        console.log("download error target " + error.target);
                        console.log("upload error code" + error.code);
                        document.getElementById("message_manifest").className = "";
                        document.getElementById("message_manifest").className = "event error_message";
                        document.getElementById('message_manifest').innerHTML = 'ERROR CANNOT DOWNLOAD DATA';
                        app.errorDownload++;
                    },
                    false,
                    {
                        headers: {
                            "Authorization": "Basic dGVzdHVzZXJuYW1lOnRlc3RwYXNzd29yZA=="
                        }
                    }
                );
                
            });
        });
    },
    downloadBulkFile: function (option, number, fileNumber, splashscreen) {
        document.getElementById('message_manifest').innerHTML = 'DOWNLOADING ' + number + ' of ' + fileNumber;
        var file = option[number];
        console.log('splashscreen1' + splashscreen);
        var fileURL = app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + file.file;
        var download_url = file.url;
        console.log('aaaa1 ' + download_url);
        console.log('aaaa1 ' + fileURL);
        var download_url_array = download_url.split("/");
        var lenght_array = 0;
        for (var key in download_url_array) {
            lenght_array++;
        }
        lenght_array--;
        download_url = '';
        for (var key in download_url_array) {
            if(key < lenght_array) {
                download_url += download_url_array[key] + '/';
            }
        }
        console.log('aaaa1 ' + download_url);
        var create_folder_location = app.androidFileLocation + '/' + app.fileLocation + download_url + '/';
        var dirManager = new DirManager();
        dirManager.create_r(create_folder_location, function() {
            var fileTransfer = new FileTransfer();
            var url_file = file.url;
            if (url_file.charAt(0) == '/') {
                url_file = url_file.replace('/', '');
            }
            var uri = encodeURI(app.URL + url_file);
            console.log("downloadFile " + app.URL + file.url);
            console.log("downloadFile " + app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + file.file);
            var serverMainfest = '';
            fileTransfer.download(
                uri,
                fileURL,
                function(entry) {
                    number++;
                    if(fileNumber > number) {
                        app.downloadBulkFile(option, number, fileNumber, splashscreen);

                    } else {
                        app.afterDownload(0);
                    }
                },
                function(error) {
                    console.log("download error source " + error.source);
                    console.log("download error target " + error.target);
                    console.log("upload error code" + error.code);
                    document.getElementById("message_manifest").className = "";
                    document.getElementById("message_manifest").className = "event error_message";
                    document.getElementById('message_manifest').innerHTML = 'ERROR CANNOT DOWNLOAD DATA';
                    app.errorDownload++;
                },
                false,
                {
                    headers: {
                        "Authorization": "Basic dGVzdHVzZXJuYW1lOnRlc3RwYXNzd29yZA==",
                        "Access-Control-Allow-Origin": "62hall.com"
                    }
                }
            );
            
        });
    },
    loadUpdateManifest: function(fileEntry) {
        fileEntry.file(function(file) {
            var reader = new FileReader();
            reader.onloadend = function(e) {
                var error = 0;
                try {
                    app.localUpdateManifest = JSON.parse(this.result);
                    console.log('ABABABABABAB');
                    console.log(app.localUpdateManifest);
                    console.log('ABABABABABAB');
                } catch(err) {
                    console.log('ERROR LOAD FILE LOCAL MANIFEST UPDATE');
                    document.getElementById("message_manifest").className = "";
                    document.getElementById("message_manifest").className = "event error_message";
                    document.getElementById('message_manifest').innerHTML = 'ERROR LOAD FILE LOCAL MANIFEST UPDATE';
                    app.errorNumber++;
                    error++;
                    if(app.errorNumber > 3) {
                        document.getElementById('message_manifest').innerHTML = 'CLOUD ERROR';
                    } else {
                        app.getUpdateManifestFromServer();
                    }
                }
                if(error == 0) {
                    document.getElementById("message_manifest").className = "";
                    document.getElementById("message_manifest").className = "event listening";
                    document.getElementById('message_manifest').innerHTML = 'CHECK UPDATE';
                    app.diffManifest();
                }
            }

            reader.readAsText(file);
        });
    },
    notFoundUpdateManifest: function(e) {
        console.log(e);
        document.getElementById("message_manifest").className = "";
        document.getElementById("message_manifest").className = "event error_message";
        document.getElementById('message_manifest').innerHTML = 'ERROR UPDATE CHECK UPDATE';
    },
    getUpdateManifestFromServer: function () {
        // app.localManifest = JSON.parse(result);
        try {
            var fileTransfer = new FileTransfer();
            var uri = encodeURI(app.manifestFileUpdateServer);
            console.log('uri ' + uri);
            var fileURL = app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + '/' + "manifestupdate.json";
            var serverMainfest = '';
            document.getElementById('message_manifest').innerHTML = 'CONNECTING TO CLOUD';
            fileTransfer.download(
                uri,
                fileURL,
                function(entry) {
                    console.log('download update complete');
                    window.resolveLocalFileSystemURL(fileURL, app.loadUpdateManifest, app.notFoundUpdateManifest);
                },
                function(error) {
                    console.log("download error source " + error.source);
                    console.log("download error target " + error.target);
                    console.log("upload error code" + error.code);
                    document.getElementById("message_manifest").className = "";
                    document.getElementById("message_manifest").className = "event error_message";
                    document.getElementById('message_manifest').innerHTML = 'ERROR CONNECTING TO CLOUD FAILED';
                },
                false,
                {
                    headers: {
                        "Authorization": "Basic dGVzdHVzZXJuYW1lOnRlc3RwYXNzd29yZA==",
                        "Access-Control-Allow-Origin": "62hall.com"
                    }
                }
            );
        } catch(err) {
            console.log('ERROR FILE TRASNFER UPDATE JSON');
        }
        
    },
    
    getSplashScreen: function () {
        cordova.getAppVersion.getPackageName(function (name) {
            jQuery.ajax({
                type: 'post',
                url: app.URL + 'home/splashscreen',
                dataType: 'json',
                async : false,
                data: {
                    name_app: name
                }
            }).done(function( data ) {
                console.log('getSplashScreen');
                var data_length = 0;
                var data_now = 0;
                var b = new FileManager();
                for (var key in data) {
                    data_length++;
                }
                for (var key in data) {
                    var temp = data[key].split(".");
                    var ext = temp[(temp.length - 1)];
                    var option = {url: data[key], file: '/' + key + '.' + ext};
                    console.log(option);
                    data_now++;
                    console.log("asem " + data[key]);
                    console.log("asem " + app.androidFileLocation + '/' + app.fileLocation + '/');
                    console.log("asem " + key + '.' + ext);
                    window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function(fileSystem) {
                            fileSystem.root.getFile(app.androidFileLocation + '/' + app.fileLocation + '/' + key + '.' + ext, {create:false}, function(fileEntry) {
                            fileEntry.remove(function(file){
                                console.log("File removed!");
                                if(data_now == data_length) {
                                    b.download_file(data[key], app.androidFileLocation + '/' + app.fileLocation + '/', key + '.' + ext, function() {
                                        app.loadSplashScreen(ext);
                                    });
                                } else {
                                    b.download_file(data[key], app.androidFileLocation + '/' + app.fileLocation + '/', key + '.' + ext, function() {
                                    });
                                }
                            },function(error){
                                console.log("error deleting the file ");
                                console.log(error);
                                });
                            },function(){
                                console.log("file does not exist 1");
                                console.log(data_now);
                                console.log(data_length);
                                if(data_now == data_length) {
                                    console.log('Start Download file');
                                    b.download_file(data[key], app.androidFileLocation + '/' + app.fileLocation, key + '.' + ext, function() {
                                        console.log('Download file succesfully');
                                        app.loadSplashScreen(ext);
                                    }, function(){
                                        console.log('Download file failed');
                                    });
                                } else {
                                    b.download_file(data[key], app.androidFileLocation + '/' + app.fileLocation, key + '.' + ext, function() {
                                    });
                                }
                            });
                            
                        },function(evt){
                            console.log(evt.target.error.code);
                    });
                }
            });
        });
    },
    loadSplashScreen: function (ext) {
        console.log('loadSplashScreen');
        var screen_width = window.screen.width;
        var screen_height = window.screen.height;
        resolveLocalFileSystemURL(app.fileProtocol + app.filePersistent + app.androidFileLocation + '/' + app.fileLocation + "/xxhdpi." + ext, function(entry) {
            last_file = entry.toURL();
            file_location = '';
            last_file = last_file.split("/");;
            for (var key in last_file) {
                if(last_file[key] == 'file') {
                    file_location += last_file[key] + '///';
                } else {
                    file_location += last_file[key];
                    if (last_file[key] != "xxhdpi." + ext) {
                        file_location += '/';
                    }
                }
            }
            console.log('file_location ss' + file_location);
            document.getElementById("main").style.background = "url(" + file_location + ")";
            var w = window.innerWidth;
            var h = window.innerHeight;
            document.getElementById("main").style.backgroundSize = w + 'px ' + h + 'px';
            // document.getElementById("splash_screen").setAttribute("src", file_location);
            if(app.firstRun == 0) {
                setTimeout(function() {
                    app.checkUpdate();
                    // document.getElementById("splash_screen").style.display= "none";
                }, 2000 );
            }
        });
    },
    backBottonEvent: function () {
        console.log('prevent is called 0');
        // var origin = window.location.origin;
    },
    checkUpdate: function() {
        // app.getSplashScreen();
        var dirManager = new DirManager();
        console.log(app.URL + 'version');
        cordova.getAppVersion.getVersionNumber(function (version) {
            jQuery.ajax({
                type: 'post',
                url: app.URL + 'home/version',
                dataType: 'json',
                async : false,
                data: {
                }
            }).done(function( data ) {
                console.log('VERSION APP');
                console.log(data);
                if(version == data) {
                    dirManager.create_r(app.androidFileLocation + '/' + app.fileLocation + '/', function() {
                        app.loadLocalManifest();
                        var storage = window.localStorage;
                        var keyname = window.localStorage.getItem("loadDone");
                    });
                } else {
                    console.log('try remove' + app.androidFileLocation + '/' + app.fileLocation + '/' + 'manifest.json');
                    window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function(fileSystem) {
                        fileSystem.root.getFile(app.androidFileLocation + '/' + app.fileLocation + '/' + 'manifest.json', {create:false}, function(fileEntry) {
                            fileEntry.remove(function(file){
                                console.log("File removed!");
                            },function(error){
                                console.log("error deleting the file ");
                                console.log(error);
                                });
                            },function(){
                                console.log("file does not exist 1");
                            });
                            
                        },function(evt){
                            console.log(evt.target.error.code);
                    });
                    document.getElementById("message_manifest").className = "";
                    document.getElementById("message_manifest").className = "event error_message";
                    document.getElementById('message_manifest').innerHTML = 'PLEASE UPDATE<br/>APPLICATION FROM<br/>GOOGLE PLAY STORE<br/>(Touch Here To Open GOOGLE PLAY STORE)';
                    document.getElementById("message_manifest").onclick = app.openPlayStore;
                }
            }).fail(function( jqXHR, textStatus ) {
                console.log( "Request page failed: " + textStatus );
                document.getElementById("message_manifest").className = "";
                document.getElementById("message_manifest").className = "event error_message";
                document.getElementById('message_manifest').innerHTML = 'ERROR CANNOT DOWNLOAD DATA';
            });
        });
    },
    openPlayStore: function () {
        cordova.getAppVersion.getPackageName(function (name) {
            cordova.plugins.market.open(name);
        });
    },
//    pushMessage: function() {
//        var push = PushNotification.init({ "android": {"senderID": app.senderID}});
//        push.on('registration', function(data) {
//            console.log(data.registrationId);
//            app.registrationId = data.registrationId;
//            app.getSplashScreen();
//        });
//        push.on('notification', function(data) {
//            console.log(data);
//            alert(data.title+" Message: " +data.message);
//            // data.title,
//            // data.count,
//            // data.sound,
//            // data.image,
//            // data.additionalData
//        });
//
//        push.on('error', function(e) {
//            document.getElementById("message_manifest").className = "";
//            document.getElementById("message_manifest").className = "event error_message";
//            document.getElementById('message_manifest').innerHTML = 'ERROR CONNECTING TO CLOUD FAILED';
//            console.log(e.message);
//        });
//    },
    // Update DOM on a Received Event
    receivedEvent: function(id) {
        var parentElement = document.getElementById(id);
        var listeningElement = parentElement.querySelector('.listening');
//        app.pushMessage();
        app.getSplashScreen();
        console.log('Received Event: ' + id);
        
    }
};

app.initialize();