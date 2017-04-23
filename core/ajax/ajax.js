/**
* Variable definition : ajax request is in progresss
* 
*/
var ajaxInProgress = false;

/**
* ajax()
* 
* send an ajax request then parse result.
* The loading animation is started.
*
* @param string uri  Ajax uri
* @param FormData data  Data for ajax body response (form)
*   formData['ajax']='ajax' is added to ajax body.
*   formData['token']=token is added to ajax body.
* @param string id  Id of the form, used for hide and show animation
*/
function ajax(uri, data, id)
{
  // console.log('ajax: '+uri);
  $('#loading').fadeTo(250,1);

  if (data === null) {
   data = new FormData();
  }
  data.append('ajax', 'ajax');
  data.append('token', getToken());

  // console.log(getToken());
  // for (var pair of data.entries()) {
  //   console.log(pair[0]+ ', ' + pair[1]); 
  // }

  $.ajaxSetup({contentType: false,processData: false});
  $.post(uri, data , function(result) {
    ok = true;
    try {
       console.log(result);
      result = JSON.parse(result);
    } catch(e) {
      ok = false;
    }

    if (ok) {
      parseAjaxResult(result, id);
    } else {
      $('#loading').fadeTo(250,0).hide(250);
      ajaxInProgress = false;
    }
  });
} // post


/**
* ajaxInit()
*
* Initialize (clear old and init new one) all events for :
*   - <a class="ajax"> : click Event
*   - <form class="ajax"> : submit Event
*   - <button class="ajax"> : click Event
* Set ajaxInProgress to false.
*/
function ajaxInit()
{
  /* ajax on <a href="#"></a> */
  $('a.ajax').unbind('click').click(function(event) {
    event.preventDefault();
    ajaxOnAnchor(this);
  });

  /* ajax on <form action=""></form> */
  $('form.ajax').submit(function(event) {
    event.preventDefault();
    ajaxOnForm(this);
  });

  /* ajax on <button type=""></form> */
  $('button.ajax').unbind('click').click(function(event) {
    event.preventDefault();
    ajaxOnButton(this);
  });
} // ajaxInit()

ajaxInit();


/**
* ajaxOnAnchor()
* 
* prepare then send ajax request:
* - uri  : a.href
* - data : null
* - id   : a.data-ajax
*
* @param object that = this of caller
*/
function ajaxOnAnchor(that)
{
  if (ajaxInProgress === false) {
    ajaxInProgress = true;
    if (that.href) {
      // data-ajax=""
      if ($(that).data('ajax')) {
        $('#'.concat($(that).data('ajax'))).fadeTo(250,0);
      }

      ajax(that.href, null, $(that).data('ajax'));
    } // if
  } // if
}


/**
* ajaxOnForm()
* 
* prepare then send ajax request:
* - uri  : form.action
* - data : FormData
* - id   : form.data-ajax
* Form.id is hided.
*
* @param object that = this of caller
*/
function ajaxOnForm(that)
{
  if (ajaxInProgress === false) {
    ajaxInProgress = true;
    // data-ajax=""
    if ($(that).data('ajax')) {
      $('#'.concat($(that).data('ajax'))).fadeTo(250,0);
    }

    var formData = new FormData(that);

    ajax(that.action, formData, $(that).data('ajax'));
  } // if
}


/**
* ajaxOnAnchor()
* 
* prepare then send ajax request:
* - uri  : button.action
* - data : null
* - id   : button.data-ajax
*
* @param object that = this of caller
*/
function ajaxOnButton(that)
{
  if (ajaxInProgress === false) {
    ajaxInProgress = true;
    ajax($(that).attr('action'), null, $(that).data('ajax'));
  } // if
}


/**
* parseAjaxResult()
* 
* Parsing the ajax response from server, severals action are defined (cf ajax.php).
* Specific management on 'innerHtml', all events are re-defined (ajaxInit).
* At the end, if necessary, the form Id is used to show for animation.
* At the end, the Loading animation is stoped.
*
* @param array data  Data from server response
* @param string id  Id of hided form
*/
function parseAjaxResult(data, id)
{
  setTimeout(function() {

    $.each(data, function(key, value) {

      // click
      if (value.action == 'click') {
        setTimeout(function() {
          $('#'.concat(value.id)).click();
        }, value.delay);  
      } // if

      // endOfAjax
      if (value.action == 'endOfAjax') {
        setTimeout(function() {
          ajaxInProgress = false;
        }, 12);  
      } // if

      // httpRequest
      if (value.action == 'httpRequest') {
        setTimeout(function() {
          ajax(value.uri, null, null);
        }, value.delay);  
      } // if

      // innerHtml
      if (value.action == 'innerHtml') {
        $('#'.concat(value.id)).html(value.innerHtml);

        if (value.id === id) {
          $('#'.concat(value.id)).fadeTo(250, 1);
        } // if
        ajaxInit();
      } // if

      // setAttr
      if (value.action == 'setAttr') {
        setTimeout(function() {
          $('#'.concat(value.id)).attr(value.attr,value.value);
        }, value.delay);  
      } // if

      // setLoading
      if (value.action == 'setLoading') {
        setTimeout(function() {
          if (value.value == 'on') {
            $('#loading').fadeTo(250,1);
          } else {
            $('#loading').fadeTo(250,0).hide(250);
          }
        }, value.delay);  
      } // if

      // token
      if (value.action == 'token') {
          setAllTokens(value.token);
      } // if

    });
  }, 12);
}


/**
* getToken()
* 
* @return string token  Value attr of input.id="token"
*/
function getToken()
{
  return $("#token").attr('value');
} // getToken()

/**
* setAllTokens()
* 
* Set all input.value to token parameters for all input with class="token"
*
* @param string token  Token to spread
*/
function setAllTokens(token)
{
  $('[name="token"]').each(function() {
    $(this).attr('value',token);
  });
} // 
