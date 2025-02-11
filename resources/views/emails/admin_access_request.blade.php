<x-mail::message>

<h3 class="text-lg font-semibold">Hello Admin!</h3><br/>

We have received a request by {{$name}} to access the {{$filetype}} whose uuid is {{$fileid}}. Below are the details:<br>

<x-mail::table>
| Field       | Value        |
| ------------- | :-----------: |
| User Name       | {{$name}}         |
| User Email       | {{$email}}       |
| File Name       | {{$filename}}       |
| File UUID       | {{$fileid}}       |
| File Type       | {{$filetype}}       |
| Message       | {{$message}}       |
</x-mail::table>

Regards,<br>
{{ config('app.name') }}
</x-mail::message>