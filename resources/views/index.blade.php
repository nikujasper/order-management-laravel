<style>
    .h {
        display: none;
        width: 65px;
    }

    .s {
        display: inline-block;
    }
</style>


<form action="orderFormAction" method="post">
    @csrf
    <label for="">ProdName:</label>
    <input type="text" id="prodname" name="prodname" required><br>

    <label for="ProdID">ProdID:</label>
    <input type="text" name="prodid" id="prodid" readonly style="border: none; outline:none; " required><br>

    <label for="ProdRate">ProdRate:</label>
    <input type="text" name="prodrate" id="prodrate" readonly style="border: none; outline:none;" required><br>

    <label for="OrderQty">OrderQty:</label>
    <input type="text" name="orderqty" id="orderqty" required><span id="spid"></span>
    <input type="text" name="oldorderqty" id="oldorderqty" hidden><br>
    <input type="text" name="orderqty2" id="orderqty2" hidden>

    <label for="OrderValue">OrderValue:</label>
    <input type="text" name="ordervalue" id="ordervalue" readonly style="border: none; outline:none;" required><br>

    <input type="text" id="upordid" name="upordid" hidden>
    <input type="submit" value="submit" name="submit">
    <input type="submit" value="update" name="Update" class="h">
    <input type="reset" value="Reset" id="reset">
</form>
<p>{{isset($message)?$message:''}}</p>

<div>
    <table border="1px" style="border-collapse: collapse; text-align:center">
        <tr>
            <th>OrderID</th>
            <th>OrderDate</th>
            <th>ProdName</th>
            <th>ProdRate</th>
            <th>OrderQty</th>
            <th>OrderValue</th>
            <th>Action</th>
        </tr>
        @foreach($orders as $row)
        <tr>
            <td>{{ $row->orderID }}
                <input type="text" value="{{ $row->orderID }}" class="ordid" hidden>
            </td>
            <td>{{ $row->orderDate }}</td>
            <td>{{ $row->prodName }}</td>
            <td>{{ $row->prodRate }}</td>
            <td>{{ $row->orderQty }}</td>
            <td>{{ $row->orderValue }}</td>
            <td>
                <button class="delete">Delete</button>
                <button class="edit" id="ordqtyL">Edit</button>
            </td>
        </tr>
        @endforeach
    </table>
</div>



<script src="{{URL::asset('js/jQuery.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $("#prodname").blur(function() {
            $.ajax({
                url: "getproducturl",
                type: "GET",
                dataType: "JSON",
                data: {
                    prodname: $("#prodname").val()
                },
                success: function(res) {
                    json_text = JSON.stringify(res);
                    obj = JSON.parse(json_text);
                    $("#prodrate").val(obj.prodRate);
                    $('#oldorderqty').val(obj.prodQty);
                    $("#prodid").val(obj.prodID);
                    $("#orderqty").val(null);
                    $("#ordervalue").val(null);
                    $('#spid').text(null);
                }
            })
        });

        $("#orderqty").blur(function() {
            var orderQty = $("#orderqty").val();
            var prodRate = $("#prodrate").val();
            var oldqty = $('#oldorderqty').val();
            var low = 1;
            if (Number(orderQty) > Number(oldqty)) {
                $('#spid').text('Max Qty ' + oldqty);
                $("#ordervalue").val(null);
            } else {
                var orderValue = orderQty * prodRate;
                $("#ordervalue").val(orderValue);
                $('#spid').text('Accepted');
            }
        });

        $('.edit').click(function() {
            var row = $(this).closest('tr');
            var ordid = row.find('[class="ordid"]').val();
            $.ajax({
                url: "getOrderDetail",
                type: "GET",
                dataType: "JSON",
                data: {
                    ordid: ordid
                },
                success: function(res) {
                    json_text = JSON.stringify(res);
                    obj = JSON.parse(json_text);
                    $("#prodrate").val(obj.prodRate);
                    $('#oldorderqty').val(obj.prodQty);
                    $('#orderqty2').val(obj.orderQty);
                    $("#prodid").val(obj.prodID);
                    $("#orderqty").val(obj.orderQty);
                    $("#ordervalue").val(obj.orderValue);
                    $("#prodname").val(obj.prodName);
                    $("#prodname").prop("disabled", true);
                    $("#upordid").val(obj.orderID);
                    $('#spid').text(null);
                }
            })
        });

        $('.delete').click(function() {
            var row = $(this).closest('tr');
            var ordid = row.find('[class="ordid"]').val();
            $.ajax({
                url: "deleteOrder",
                type: "GET",
                dataType: "JSON",
                data: {
                    ordid: ordid
                },
                success: function(res) {
                    location.reload();
                    console.log('success');
                }
            })
        });

        $('#reset').click(function() {
            $("#prodname").prop("disabled", false);

        });

    })
</script>