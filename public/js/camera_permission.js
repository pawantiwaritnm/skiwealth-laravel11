$(document).ready(function(){

  if (!"geolocation" in navigator) {
      alert("No geolocation available!");

    }
    var positionDenied = function () {
      console.log("Location Denied");
    };
    var latvalue;
    var lngvalue;
    var state;
    var city;
    let image;
     var revealPosition = function (position) {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;
      // Location IQ API (10K and 2 req per second free)
      $.ajax({

        type: "get",

        url: "https://us1.locationiq.com/v1/reverse.php",

        data: {

          key: "1f9e8bbcd218fb",

          lat: lat,

          lon: lng,

          format: "json",

        },

        success: function (response) {

            latvalue = lat;

            lngvalue = lng;

            city = response.address.city;

            state = response.address.state;

        },

      });

    };

    var geoSettings = {

      enableHighAccuracy: true,

      maximumAge: 30000,

      timeout: 20000,

    };

    function handleLocationPermission() {
    if (navigator.permissions && navigator.permissions.query) {
      navigator.permissions.query({ name: "geolocation" }).then(function (result) {
        if (result.state == "granted") {
          report(result.state);
            navigator.geolocation.getCurrentPosition(
              revealPosition,
              positionDenied,
              geoSettings
            );

        } else if (result.state == "prompt") {
          
          alert("Please enable location permission from settings and try again!");
          report(result.state);

          navigator.geolocation.getCurrentPosition(
            revealPosition,
            positionDenied,
            geoSettings

          );

        } else if (result.state == "denied") {

            alert("Please enable location permission from settings and try again!");

          report(result.state);

          return result.state;

        }

        result.onchange = function () {

          report(result.state);

        };

      });
    }else if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          revealPosition,
          positionDenied,
          geoSettings
        );
      }

    }

    function report(state) {

      console.log("Permission: " + state);

    }
   
    handleLocationPermission();
    // handle camera Permission
   function handleCameraPermission(){
    if (navigator.permissions && navigator.permissions.query) {
        navigator.permissions.query({ name: "camera" }).then(res => {
          if(res.state == "granted"){
              // has permission
          }else if(res.state == "prompt"){
            alert("Please enable camera permission from settings and try again!");
          }else if(res.state == "denied"){
            alert("Please enable camera permission from settings and try again!");
          }
      });
     }
    }
    handleCameraPermission();
    // Store a reference of the preview video element and a global reference to the recorder instance

      let constraintObj = { 

          audio: false, 

          video: { 

              facingMode: "user", 

              width: { min: 640, ideal: 1280, max: 1920 },

              height: { min: 480, ideal: 720, max: 1080 } 

          } 

      }; 



      // width: 1280, height: 720  -- preference only

      // facingMode: {exact: "user"}

      // facingMode: "environment"

      //handle older browsers that might implement getUserMedia in some way

      if (navigator.mediaDevices === undefined) {

          navigator.mediaDevices = {};

          navigator.mediaDevices.getUserMedia = function(constraintObj){

              let getUserMedia =  navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
              if (!getUserMedia) {  

                  return Promise.reject(new Error('getUserMedia is not implemented in this browser'));

              }

              return new Promise(function(resolve, reject) { 

                  getUserMedia.call(navigator, constraintObj, resolve, reject);

              });

          }

      }else{

          navigator.mediaDevices.enumerateDevices()

          .then(devices => {

              devices.forEach(device=>{

                  console.log(device.kind.toUpperCase(), device.label);

                  //, device.deviceId

              })

          })

          .catch(err=>{

              console.log(err.name, err.message);

          })

      }



      navigator.mediaDevices.getUserMedia(constraintObj)

      .then(function(mediaStreamObj) { 

          //connect the media stream to the first video element
          $('#vid2').hide();
          // alert($('.ipv-otp').text());
          let video = document.getElementById('vid1');
          let canvas = document.getElementById('canvas');
          let  photo = document.getElementById('photo');

          if ("srcObject" in video) {

              video.srcObject = mediaStreamObj;
              

          } else {

              //old version

              video.src = window.URL.createObjectURL(mediaStreamObj);

          }

          

          video.onloadedmetadata = function(ev) {

              //show in the video element what is being captured by the webcam

              video.play();

          };

          

          //add listeners for saving video/audio
          let width = 320;    // We will scale the photo width to this
          let height = 0;
          let start = document.getElementById('btnStart');

          let redeo_ipv = document.getElementById('redeo_ipv');

          let save_ipv = document.getElementById('save_ipv');

          let vidSave = document.getElementById('vid2');

          let mediaRecorder = new MediaRecorder(mediaStreamObj);

          let chunks = [];

          start.addEventListener('click', (ev)=>{

                $("#btnStart").hide();

                $("#ipv_progress").show();

                mediaRecorder.start();
                height = video.videoHeight / (video.videoWidth/width);
                takepicture();
                setTimeout(StopRecording, 4000);

          })

          redeo_ipv.addEventListener('click', (ev)=>{

              location.reload();

              $("#btnStart").show();

              $('#save_ipv').hide();

              $('#redeo_ipv').hide();

              $('#vid1').show();

              $('#vid2').hide();

              // mediaRecorder.start();

              // setTimeout(StopRecording, 4000);

          })
          function takepicture() {
            var context = canvas.getContext('2d');
            if (width && height) {
                canvas.width = width;
                canvas.height = height;
                context.drawImage(video, 0, 0, width, height);
        
                image = canvas.toDataURL('image/png');
                photo.setAttribute('src', image);
            } else {
                clearphoto();
            }
        }
        function clearphoto() {
          var context = canvas.getContext('2d');
          context.fillStyle = "#AAA";
          context.fillRect(0, 0, canvas.width, canvas.height);
      
          var data = canvas.toDataURL('image/png');
          photo.setAttribute('src', data);
      }
          function StopRecording(){

              video.pause();

              video.currentTime = 0;

              mediaRecorder.stop();

              //mediaStreamObj.stop();

              $("#ipv_progress").hide();

          }

       

          mediaRecorder.ondataavailable = function(ev) {

              chunks.push(ev.data);

          }

        

          mediaRecorder.onstop = (ev)=>{

              let blob = new Blob(chunks, { 'type' : 'video/mp4;' });

              // chunks = [];

              let videoURL = window.URL.createObjectURL(blob);

              vidSave.src = videoURL;
              $('#vid2').show();
              $('#save_ipv').show();

              $('#redeo_ipv').show();

              $('#vid1').hide();

          }

          // this function is used to generate random file name

          function getFileName(fileExtension) {

              var d = new Date();

              var year = d.getUTCFullYear();

              var month = d.getUTCMonth();

              var date = d.getUTCDate();

              return 'RecordRTC-' + year + month + date + '-' + getRandomString() + '.' + fileExtension;

          }

          

          function getRandomString() {

              if (window.crypto && window.crypto.getRandomValues && navigator.userAgent.indexOf('Safari') === -1) {

                  var a = window.crypto.getRandomValues(new Uint32Array(3)),

                      token = '';

                  for (var i = 0, l = a.length; i < l; i++) {

                      token += a[i].toString(36);

                  }

                  return token;

              } else {

                  return (Math.random() * new Date().getTime()).toString(36).replace(/\./g, '');

              }

          }

          save_ipv.addEventListener('click', (ev)=>{
            

              let blob = new Blob(chunks, { 'type' : 'video/mp4;' });

              let fileName = getFileName('webm');

              let fileObject = new File([blob], fileName, {

                 type: 'video/webm',

                 mimeType: 'video/webm;codecs=vp9',

              });

              var formData = new FormData();
              formData.append('video-filename', fileObject.name);
              formData.append("image", image);
              formData.append('state', state);
              formData.append('ipv_otp', $('.ipv-otp').text());
              formData.append('lat', latvalue);
              formData.append('lng', lngvalue);
              formData.append('city', city);
              formData.append('video-blob', fileObject);

              jQuery.ajax({

                  url: "User_record_video",

                  type: "post",

                  dataType: "json",

                  contentType: "application/octet-stream",

                  enctype: "multipart/form-data",

                  contentType: false,

                  processData: false,

                  data: formData,

                  success: function (result) {
                    if(result.status == -1){
                      window.location.href = '/ipvPermission/';
                    }else{
                      alert(result.message); 
                      window.location.href = '/';
                      //setTimeout(function(){ window.location.href = '/ski/ipv'; }, '2000');
                    }  
                  },

                });

             

          })

         

      })

      .catch(function(err) { 

          let start = document.getElementById('btnStart');

          start.addEventListener('click', (ev)=>{ 
               handleLocationPermission();
               handleCameraPermission();

              // if(err.message=="Permission dismissed"){

              //     alert("Unable to Access WebCam. Please restart the browser and try again");

              // }else if(err.message=="Permission denied"){

              //     alert("Unable to Access WebCam. Please restart the browser and try again");

              // }

        })

          console.log(err.name, err.message); 

      });

      



});