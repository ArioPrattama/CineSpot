<?php

session_start();

if(!isset($_SESSION['UserID'])){
    header("Location: ../../login.php");
    exit;
}

include "../../config/database.php";

if($_SERVER['REQUEST_METHOD'] != "POST"){
    header("Location: ../../index.php");
    exit;
}

$UserID = $_SESSION['UserID'];
$ScheduleID = (int)$_POST['ScheduleID'];
$seat = $_POST['seat'];

$fnb = isset($_POST['fnb']) ? $_POST['fnb'] : [];

$promoCode = trim($_POST['promo']);

$query = mysqli_query($conn,"
SELECT *
FROM Schedule
WHERE ScheduleID='$ScheduleID'
");

$schedule = mysqli_fetch_assoc($query);

$ticketPrice = $schedule['ticketPrice'];

$totalTicket = count($seat) * $ticketPrice;

$totalFnB = 0;

foreach($fnb as $FnBID => $qty){

    if($qty > 0){

        $item = mysqli_fetch_assoc(
            mysqli_query($conn,"
            SELECT *
            FROM FnB_Item
            WHERE FnBID='$FnBID'
            ")
        );

        $totalFnB += $item['price'] * $qty;

    }

}

$discount = 0;

if($promoCode != ""){

    $promo = mysqli_query($conn,"
    SELECT *
    FROM Promo
    WHERE promoCode='$promoCode'
    AND status='Active'
    ");

    if(mysqli_num_rows($promo)){

        $promoData = mysqli_fetch_assoc($promo);

        $subtotal = $totalTicket + $totalFnB;

        if($subtotal >= $promoData['minimumPurchase']){

            $discount =
            ($promoData['discount']/100)
            * $subtotal;

            $PromoID = $promoData['PromoID'];

        }else{

            $PromoID = "NULL";

        }

    }else{

        $PromoID = "NULL";

    }

}else{

    $PromoID = "NULL";

}

$totalAmount =
$totalTicket +
$totalFnB -
$discount;

$qris = "QRIS".time();

$sql = "

INSERT INTO Transaction

(

UserID,

ScheduleID,

PromoID,

qris,

totalAmount,

paymentStatus,

createdDate

)

VALUES

(

'$UserID',

'$ScheduleID',

$PromoID,

'$qris',

'$totalAmount',

'Paid',

NOW()

)

";

mysqli_query($conn,$sql);

$TransactionID =
mysqli_insert_id($conn);

foreach($seat as $SeatID){

    $SeatID = (int)$SeatID;

    mysqli_query($conn,"

    INSERT INTO Transaction_Seat

    (

    TransactionID,

    SeatID

    )

    VALUES

    (

    '$TransactionID',

    '$SeatID'

    )

    ");

    mysqli_query($conn,"
    UPDATE Seat
    SET status='Booked'
    WHERE SeatID='$SeatID'
    ");

}

mysqli_query($conn,"
UPDATE Schedule
SET availableSeat = availableSeat - ".count($seat)."
WHERE ScheduleID='$ScheduleID'
");

foreach($fnb as $FnBID=>$qty){

    if($qty>0){

        $item=mysqli_fetch_assoc(

        mysqli_query($conn,"

        SELECT *

        FROM FnB_Item

        WHERE FnBID='$FnBID'

        ")

        );

        $subtotal=
        $item['price']*$qty;

        mysqli_query($conn,"

        INSERT INTO Transaction_FnB_Item

        (

        TransactionID,

        FnBID,

        quantity,

        subtotal

        )

        VALUES

        (

        '$TransactionID',

        '$FnBID',

        '$qty',

        '$subtotal'

        )

        ");

    }

}

header("Location: success.php?id=".$TransactionID);
exit;

