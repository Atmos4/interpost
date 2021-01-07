function clickImage(){
    document.getElementById('profile-picture-upload').click();
}

function changeImage(e){
    if (e.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e){
          document.querySelector('#profile-picture').setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(e.files[0]);
      }
}