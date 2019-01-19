<form action="/action_page.php">
  
    
    <div class="id_100">
        <select>
        @foreach($students as $student)
            <option value="val2">{{$student->name}}</option>
        @endforeach
        </select>
    </div>
  Last name:<br>
  <input type="text" name="lastname" value="Mouse">
  <br><br>
  <input type="submit" value="Submit">
</form> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script>
$("div.id_100 select").val("val2");
</script>