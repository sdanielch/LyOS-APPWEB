
    <div id="buttons" style="text-align: center; display: block">
    <input id="query" class="uk-form uk-input" placeholder="Buscar en YouTube" value='' type="text"/><button id="search-button" class="uk-button uk-button-danger uk-form uk-width-1-1"   onclick="keyWordsearch()">Buscar</button>   
</div>
    <div id="container">
    <h1></h1>
    <div id="results" style="display: block; text-align: center;"></div>
    </div>           
<script>
 function keyWordsearch(){
    gapi.client.setApiKey('AIzaSyC17uD7yboTlRlgxomUsMqn-c9T7R_y_vE');
    gapi.client.load('youtube', 'v3', function(){
            makeRequest();
    });
}
function makeRequest(){
    var q = $('#query').val();
    var request = gapi.client.youtube.search.list({
            q: q,
            part: 'snippet', 
            maxResults: 20
    });
    request.execute(function(response)  {                                                                                    
            $('#results').empty()
            var srchItems = response.result.items;                      
            $.each(srchItems, function(index, item){
            var vidTitle = item.snippet.title;  
            //console.log(item.id.videoId)
            videoid = item.id.videoId;
            vidThumburl =  item.snippet.thumbnails.default.url;                 
            vidThumbimg = '<pre><img id="thumb" src="'+vidThumburl+'" alt="No  Image  Available." style="width:204px;height:128px"></pre>';   
            var url = "https://www.youtube-nocookie.com/embed/" + videoid + "?autoplay=true";
            video = '<iframe width="240" height="200" src="https://www.youtube.com/embed/'+videoid+'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            imagenes = '<img src="'+vidThumburl+'" style="width: 240px; height: 200px;" />';               

            $('#results').append('<div id='+videoid+' style="cursor: pointer; margin: 4px;border: 1px rgba(120,120,120,0.4) solid; border-radius: 4px; display: inline-block;position: relative; width: 260px; height: 240px; overflow: hidden; text-align: center;white-space: nowrap; text-overflow: ellipsis; padding: 10px;">'+imagenes+'<br />'+vidTitle+'</div>');                  

            $("#"+videoid).on("click", function(e) {
                //UIkit.modal.dialog(video);
                createwindow({nombre: 'LineTube - ' + vidTitle, tipo: 'iframe', pi: true, contenido: url, ancho: 800, alto: 450, icono: '<?php echo $img;?>youtube.png'})
            });

    })  
  })  
}
keyWordsearch();
 </script> 
 <script src="https://apis.google.com/js/client.js?onload=googleApiClientReady"></script>
