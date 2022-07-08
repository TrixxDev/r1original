<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingForm extends Model
{
    public $vehicleMake;
    public $vehicleModel;
    public $vehiclePlate;
    public $purpose;
    public $storageBin;
    public $comment;
    public $ownerName;
    public $ownerPhone;
    public $ownerEmail;
}
