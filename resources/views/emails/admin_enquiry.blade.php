<x-mail::message>

<h3 class="text-lg font-semibold">Hello Admin!</h3><br/>

We have received an enquiry. Below are the details:<br>

<x-mail::table>
| Field       | Value        |
| ------------- | :-----------: |
| Name       | {{$data->name}}         |
| Email       | {{$data->email}}       |
| Phone       | {{$data->phone}}       |
| IP       | {{$data->ip_address}}       |
| Subject       | {{$data->subject}}       |
| Message       | {{$data->message}}       |
</x-mail::table>

Regards,<br>
{{ config('app.name') }}
</x-mail::message>