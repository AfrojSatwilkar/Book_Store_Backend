<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOrderDetails extends Notification implements ShouldQueue
{
    use Queueable;

    public $order_id;
    public $bookName;
    public $bookAuthor;
    public $quantity;
    public $totalPrice;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order_id, $bookName, $bookAuthor, $quantity, $totalPrice)
    {
        $this->order_id = $order_id;
        $this->bookName = $bookName;
        $this->bookAuthor = $bookAuthor;
        $this->quantity = $quantity;
        $this->totalPrice = $totalPrice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Yo have Placed an Order From BookStore App.')
                    ->line('your order is confirmed.')
                    ->line('Your Order Summary is : ')
                    ->line('Order_Id : '.$this->order_id)
                    ->line('Book Name : '. $this->bookName)
                    ->line('Book Author : '. $this->bookAuthor)
                    ->line('Book Quantity : '. $this->quantity)
                    ->line('Total Payment : '. $this->totalPrice)
                    ->line('Save the OrderId For Further Communication')
                    ->line('For Further Querry Contact This Email Id')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
