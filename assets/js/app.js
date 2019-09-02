/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');


//style.setProperty()

// let variation = document.querySelectorAll(".variation");
// for(let i = 0; i <= 100; i++){
//     if(parseFloat(variation[i].textContent) < 0){
//         variation[i].style.color = "red";
//     }else{
//         variation[i].style.color = "green";
//     }
// }

 fetch('http://127.0.0.1:8000/getcrypto')
//fetch('http://localhost/CryptoCheck/public/getcrypto')
  .then(function(response) {
      debugger;
    return response.json();
  })
  .then(function(myJson) {
    for (let crypto in myJson)
    {
        debugger;
    }
  });


