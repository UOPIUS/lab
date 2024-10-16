/**
 * 
 * @param {Object} e 
 * @param {Object} responseDiv 
 * @param {String} url 
 * @param {Object} params 
 */
function makeXHR(e,responseDiv,url,params) {
    e.preventDefault();
    //let form = this, //e.target;
    var data = new URLSearchParams(params).toString()
    responseDiv.innerHTML = '<p class="bg-warning p-2 text-white text-center">Please Wait. . .</p>';
    let xhr = new XMLHttpRequest();
    xhr.open('POST', url);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(data);
    xhr.onload = function() {
        if (xhr.status == 200) {
            var jsonData = JSON.parse(xhr.response);
            if (200 == jsonData.status) {
                responseDiv.innerHTML = '<p class="bg-success p-2 text-white text-center">' + jsonData.message + '</p>';
                setTimeout(function() {
                    switch (params.action) {
                        case 'login':
                            window.location.replace(jsonData.url);
                        break;
                        case 'sf0':
                            window.location.href = window.location.href;
                        break;
                        case 'sf1':
                            const queryString = window.location.search;
                            const urlParams = new URLSearchParams(queryString);
                            const ref_redir = urlParams.get('ini_id');
                            window.location.href = "https://lab.capitalmedicares.com/client_profile.php?refx="+ref_redir;
                        break;
                        default:
                            window.location.reload();
                        break;
                    }
                }, 2000)
                return false;
            } else {
                responseDiv.innerHTML = '<p class="bg-danger p-2 text-white text-center">' + jsonData.message + '</p>';
                setTimeout(function() {
                    responseDiv.innerHTML = '';
                }, 1000)
            }
        }
        return false;
    };
    xhr.onerror = function() {
        console.log("Request failed");
    };
}

function displayBlock(param) {
    let button = param,
        buttonText = button.getAttribute('data-name');
    var mainDiv = document.getElementById("divToShow");
    var y = document.getElementById("divToHide");
    if (mainDiv.style.display === "none") { //hide outer container
        mainDiv.style.display = "block";
        y.classList.add('d-none');
        if (button.textContent != 'undefined') {
            button.textContent = buttonText
        } else {
            button.inneText = buttonText
        }
    } else {
        mainDiv.style.display = "none";
        y.classList.remove('d-none');
        if (button.textContent != 'undefined') {
            button.textContent = 'Cancel'
        } else {
            button.inneText = 'Cancel'
        }   
    }
}

function loadLocals(e) {
    var id = e.value;
    var data = new URLSearchParams({s:id}).toString()
    var locals = document.getElementById("lga");
    locals.innerHTML = "<option value=''>-Select - </option>"
    let xhr = new XMLHttpRequest();
    xhr.open('POST', '../requests/lga.php');
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(data);
    xhr.onload = function() {
        if (xhr.status != 200) {
            console.log(xhr.statusText);
        } else {
            var jsonData = JSON.parse(xhr.response);
            var jsonLength = jsonData.data.length;
            for (var i = 0; i < jsonLength; i++) {
                var counter = jsonData.data[i];
                var newSelect = document.createElement("option");
                newSelect.value = counter.lid;
                newSelect.text = counter.name;
                locals.appendChild(newSelect);
            }
        }
    };
}



function removeOverlay() {
    const overLay = document.getElementById('customOverlay');
    overLay.style.display = "none";
    overLay.remove();
}

function showOverlay(message = 'Please Wait . . .'){
    const overLay = document.createElement('div');
    overLay.id = 'customOverlay';
    overLay.style.position='fixed';
    overLay.style.textAlign='center';
    overLay.style.display= 'block';
    overLay.style.width= 100+'%';
    overLay.style.height= 100+'%';
      overLay.style.top=0;
      overLay.style.color=2.5+'em';
      overLay.style.margin='auto';
    overLay.style.left= 0;
    overLay.style.right= 0;
    overLay.style.bottom= 0;
    overLay.style.backgroundColor='rgba(0,0,0,0.5)';
    overLay.style.zIndex= 999;
    overLay.style.cursor= 'pointer';
    
    const messageLayer = document.createElement('p');
    messageLayer.style.fontSize=4+'em';
    messageLayer.style.marginLeft=18+'%';
    messageLayer.style.marginTop=20+'%';
    messageLayer.style.color='#fff';
    messageLayer.innerText=message;
    overLay.appendChild(messageLayer);
    document.body.appendChild(overLay);
}
function setSelectedIndex(s, valsearch){
    // Loop through all the items in drop down list
    for (i = 0; i < s.options.length; i++)
    {
        if (s.options[i].value == valsearch)
        {
            s.options[i].selected = true;
            break;
        }
    }
    return;
}
function loadCategory(param) {
    showOverlay();
    var data = new URLSearchParams(param).toString()
    var responseHolder = document.getElementById("testOptions");
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'request/ajax_category.php');
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(data);
    xhr.onload = function() {
        removeOverlay();
        if (xhr.status != 200) {
            console.log(xhr.statusText);
        } else {
            var jsonData = JSON.parse(xhr.response);
            var jsonLength = jsonData.data.length;
            for (var i = 0; i < jsonLength; i++) {
                var counter = jsonData.data[i];
                var option = document.createElement("p");
                option.setAttribute('onclick',"addToChoice(this)")
                option.innerText = counter.name;
                option.setAttribute('data-ref',counter.tid);
                option.setAttribute('data-cost',counter.cost);
                option.classList.add('chosenValue');
                option.classList.add('p-2');
                responseHolder.appendChild(option);
            }
        }
    };
}
function removeChoice(param) {
    param.remove();
}

function addToChoice(e) {
    let selectedValue = e.getAttribute('data-cost');
    let p = document.createElement('p');
    p.setAttribute('data-referrer', e.getAttribute('data-ref'));
    p.classList.add('selected-choices');
    p.classList.add('p-1');
    p.setAttribute('onclick', 'removeChoice(this)')
    let newValue = e.innerText + ' : ' + 'N' +selectedValue;
    p.innerText = newValue;
    let testsTaken = document.getElementById('testsTaken');
    testsTaken.appendChild(p);
}
function iniNewTest(param) {
    if(confirm("You are about to Submit this Form and You cannot Edit After Submission. Are you Sure You Want to Continue?") === true){
        //get all selected tests to be performed...
        let div = document.getElementById('testsTaken').querySelectorAll('.selected-choices'),
            buildString = '',
            kounter = div.length;
        for (let index = 0; index < kounter; index++) {
            const elem = div[index];
            const test_id = elem.getAttribute('data-referrer');
            buildString += test_id + ';';
        }
        let responseDiv = document.getElementById('newTestResponse');
        makeXHR(param,responseDiv,"request/mk_tranx.php",{
            testsTaken: buildString,
            referral: document.getElementById('referrals').value,
            token: document.getElementsByTagName("meta")["csrf_token"].getAttribute("content"),
            userRef:document.getElementById('userRef').value,
            sst: 's1f',
            action: 'sf1'
        })
    }
    return false;
}

function showPlainText() {
    const passwords = document.querySelectorAll(".password-field");
    passwords.forEach(element => {
        if (element.type === "password") {
            element.type = "text";
        } else {
            element.type = "password";
        }
    });
} 

const ajax = (url)=>{
    return new Promise((resolve, reject) => {
        let xhr = new XMLHttpRequest();
    
        xhr.open('GET', url);
        xhr.send();
    
        xhr.onload = function() {
            if (xhr.status != 200) { 
                alert(`Error ${xhr.status}: ${xhr.statusText}`); // e.g. 404: Not Found
            } else {

                resolve(JSON.parse(xhr.response));
            }
        };
    /*
        xhr.onprogress = function(event) {
            if (event.lengthComputable) {
                alert(`Received ${event.loaded} of ${event.total} bytes`);
            } else {
                alert(`Received ${event.loaded} bytes`); // no Content-Length
            }
    
        };
    */
    
        xhr.onerror = function() {
            alert("Request failed");
        };
    
    });
}
function rmvBoxEvt(evt) {
    const payloadTemp = JSON.parse(evt.getAttribute("data-scdljload"));
    let finalTestCost = document.getElementById('finalTestCost');
    const newValue = Number(finalTestCost.textContent) - Number(payloadTemp.chosenTestCost);
    finalTestCost.innerHTML = newValue;
    evt.parentNode.parentNode.remove();
}

(function(window){
	window.htmlentities = {
		/**
		 * Converts a string to its html characters completely.
		 *
		 * @param {String} str String with unescaped HTML characters
		 **/
		encode : function(str) {
			var buf = [];
			
			for (var i=str.length-1;i>=0;i--) {
				buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
			}
			
			return buf.join('');
		},
		/**
		 * Converts an html characterSet into its original character.
		 *
		 * @param {String} str htmlSet entities
		 **/
		decode : function(str) {
			return str.replace(/&#(\d+);/g, function(match, dec) {
				return String.fromCharCode(dec);
			});
		}
	};
})(window);