<?php
use Illuminate\Support\Facades\Session;

$userid = Session::get('user')['name'];

?>


<div class="deznav">
<div class="deznav-scroll">
<ul class="metismenu" id="menu">
<li class="nav-label">Transactions</li>
<li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24" /><polygon fill="#000000" opacity="0.3" points="5 7 5 15 19 15 19 7" /> <path d="M11,19 L11,16 C11,15.4477153 11.4477153,15 12,15 C12.5522847,15 13,15.4477153 13,16 L13,19 L14.5,19 C14.7761424,19 15,19.2238576 15,19.5 C15,19.7761424 14.7761424,20 14.5,20 L9.5,20 C9.22385763,20 9,19.7761424 9,19.5 C9,19.2238576 9.22385763,19 9.5,19 L11,19 Z" fill="#000000" opacity="0.3" /><path d="M5,7 L5,15 L19,15 L19,7 L5,7 Z M5.25,5 L18.75,5 C19.9926407,5 21,5.8954305 21,7 L21,15 C21,16.1045695 19.9926407,17 18.75,17 L5.25,17 C4.00735931,17 3,16.1045695 3,15 L3,7 C3,5.8954305 4.00735931,5 5.25,5 Z" fill="#000000" fill-rule="nonzero" /></g></svg>
<span class="nav-text">Transactions</span>
</a>

<ul aria-expanded="false">




<li><a href="{{ url('reports') }}">Assets Addition Register</a></li>
<li><a href="{{ url('dep') }}">Assets Deletion Register</a></li>
<li><a href="{{ url('transfer') }}">Assets Transfer Register</a></li>
<li><a href="{{ url('register') }}">Assets Registers</a></li>
<li><a href="{{ url('reinsurace') }}">Reinsurance Report</a></li>
<li><a href="{{ url('broker') }}">Broker Report</a></li>
<li><a href="{{ url('premium') }}">Premium Outstanding Report</a></li>
<li><a href="{{ url('renewal') }}">Renewal Report</a></li>
<li><a href="{{ url('user') }}">User General ledger Report</a></li>
<li><a href="{{ url('uio') }}">User status Report</a></li>

<li><a href="{{ url('cr4') }}">O/S Settlement Claims Report</a></li>
<li><a href="{{ url('claim-payment') }}">Claim Payments Report </a></li>

<a href="{{ route('gl.paid.reports') }}" class="nav-link"><span>Paid Claim Payments Report</span></a>






</ul>

</li>


<li class="nav-label">Utils</li>
<li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><rect fill="#000000" opacity="0.3" x="4" y="4" width="8" height="16"></rect><path d="M6,18 L9,18 C9.66666667,18.1143819 10,18.4477153 10,19 C10,19.5522847 9.66666667,19.8856181 9,20 L4,20 L4,15 C4,14.3333333 4.33333333,14 5,14 C5.66666667,14 6,14.3333333 6,15 L6,18 Z M18,18 L18,15 C18.1143819,14.3333333 18.4477153,14 19,14 C19.5522847,14 19.8856181,14.3333333 20,15 L20,20 L15,20 C14.3333333,20 14,19.6666667 14,19 C14,18.3333333 14.3333333,18 15,18 L18,18 Z M18,6 L15,6 C14.3333333,5.88561808 14,5.55228475 14,5 C14,4.44771525 14.3333333,4.11438192 15,4 L20,4 L20,9 C20,9.66666667 19.6666667,10 19,10 C18.3333333,10 18,9.66666667 18,9 L18,6 Z M6,6 L6,9 C5.88561808,9.66666667 5.55228475,10 5,10 C4.44771525,10 4.11438192,9.66666667 4,9 L4,4 L9,4 C9.66666667,4 10,4.33333333 10,5 C10,5.66666667 9.66666667,6 9,6 L6,6 Z" fill="#000000" fill-rule="nonzero"></path></g></svg>
<span class="nav-text">Utils</span>
</a>
<ul aria-expanded="false">
<li><a href="{{ url('changePassword') }}">Change Password</a></li>
<li><a href="{{'logout'}}">Logout</a></li>
</ul>
</li>
</ul>
</div>
</div>

