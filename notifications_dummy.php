<style> 
#panel120, #flip120 {
  padding: 5px;
  text-align: center;
  background-color: #e5eecc;
  border: solid 1px #c3c3c3;
}
#panel120 {
  padding: 50px;
  /*display: none;*/
}
</style>

<div id="panel120">Hello world!</div>


<script> 
$(document).ready(function(){
  $("#flip120").click(function(){
    $("#panel120").slideToggle("slow");
  });
});
</script>