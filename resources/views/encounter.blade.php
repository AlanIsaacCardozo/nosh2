<strong>{{ trans('nosh.patient_name') }}: </strong>{!! $patientName !!}<br>
<strong>{{ trans('nosh.practice_name') }}: </strong>{!! $practiceName !!}<br>
<strong>{{ trans('nosh.address') }}: </strong>{!! $address !!}<br>
<strong>{{ trans('nosh.phone') }}: </strong>{!! $phone !!}<br>
<strong>{{ trans('nosh.fax') }}: </strong>{!! $fax !!}<br>
<strong>{{ trans("nosh.website") }}: </strong> {!! $website !!}<br>

<strong>{{ trans('nosh.encounter_age') }}: </strong>{!! $age1 !!}<br>
<strong>{{ trans('nosh.encounter_dos') }}: </strong>{!! $encounter_DOS !!}<br>
<strong>{{ trans('nosh.encounter_provider') }}: </strong>{!! $encounter_provider !!}<br>
<strong>{{ trans("nosh.encounter_status") }}: </strong> {!! $status !!}<br>
<hr />
<h4>{{ trans('nosh.encounter_cc') }}:</h4>
<p class="view">{!! $encounter_cc !!}</p>
{!! $hpi !!}
{!! $ros !!}
{!! $oh !!}
{!! $mtm !!}
{!! $vitals !!}
{!! $pe !!}
{!! $images !!}
{!! $labs !!}
{!! $assessment !!}
{!! $procedure !!}
{!! $orders !!}
{!! $rx !!}
{!! $plan !!}
<br />
<hr />
{!! $billing !!}
