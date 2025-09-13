<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unstyled Profile Page</title>
</head>

<body>
    <div>
        <!-- Header -->
        <table width="100%" cellspacing="0" cellpadding="20">
            <tr>
                <td colspan="2"><strong>{{ $record->full_name }}'s Records</strong> - {{ Auth::user()->station->name }}
                </td>
                <td align="right">

                </td>
            </tr>
        </table>

        <!-- Main Content -->
        <div>
            <!-- Personal Record Section -->
            <br>
            <table width="100%" cellspacing="0" cellpadding="10">
                <tr>
                    <td colspan="3">
                        <h2>Personal Record</h2>
                    </td>
                </tr>
                <tr>
                    {{-- <td width="33%">
                        <strong>
                            <p>Prisoner Photo</p>
                        </strong>
                        <div>
                            <img src="{{ asset('gps-logo.png') }}" alt="{{ $record->full_name }}'s Photo" width="100"
                                height="100">
                        </div>
                    </td> --}}
                    <td width="33%">
                        <strong>
                            <p>Serial Number</p>
                        </strong>
                        <p>{{ $record->serial_number }}</p>
                    </td>
                    <td width="33%">
                        <strong>
                            <p>Name of Prisoner</p>
                        </strong>
                        <p>{{ $record->full_name }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>
                            <p>Age on Admission</p>
                        </strong>
                        <p>{{ $record->age_on_admission }}years</p>
                    </td>
                    <td>
                        <strong>
                            <p>Offence</p>
                        </strong>
                        <p>{{ $record->latestSentenceByDate->offence }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>Sentence</p>
                        </strong>
                        <p>{{ $record->latestSentenceByDate->total_sentence }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>
                            <p>Date of Admission</p>
                        </strong>
                        <p>{{ $record->admission_date }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>Date of Sentence</p>
                        </strong>
                        <p>{{ $record->latestSentenceByDate->date_of_sentence }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>EPD</p>
                        </strong>
                        <p>{{ $record->latestSentenceByDate->EPD }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>
                            <p>LPD</p>
                        </strong>
                        <p>{{ $record->latestSentenceByDate->LPD }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>Court of Committal</p>
                        </strong>
                        <p>{{ $record->latestSentenceByDate->court_of_committal }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>Block & Cell</p>
                        </strong>
                        <p> {{ $record->cell?->block }} CELL {{ $record->cell->cell_number }} </p>
                    </td>
                </tr>
            </table>

            <br>

            <!-- Transfer-In Information -->
            <table width="100%" cellspacing="0" cellpadding="10">
                <tr>
                    <td colspan="3">
                        <h2>Transfer-In Information</h2>
                    </td>
                </tr>
                <tr>
                    <td width="33%">
                        <strong>
                            <p>Transferred Inmate</p>
                        </strong>
                        <p>
                            {{ $record->transferred_in ? 'Yes' : 'No' }}
                        </p>
                    </td>
                    @if ($record->transferred_in)
                    <td width="33%">
                        <strong>
                            <p>Station Transferred From</p>
                        </strong>
                        <p>{{ !empty($record->station_transferred_from_id) ? \App\Models\Station::where('id',
                            $record->station_transferred_from_id)->pluck('name')->first() : '' }}</p>
                    </td>
                    <td width="33%">
                        <strong>
                            <p>Transferred Date</p>
                        </strong>
                        <p>{{ $record->date_transferred_in }}</p>
                    </td>
                    @endif
                </tr>
            </table>

            <!-- Social Background Section -->
            <br>
            <table width="100%" cellspacing="0" cellpadding="10">
                <tr>
                    <td colspan="3">
                        <h2>Social Background</h2>
                    </td>
                </tr>
                <tr>
                    <td width="33%">
                        <strong>
                            <p>Tribe</p>
                        </strong>
                        <p>{{ $record->tribe }}</p>
                    </td>
                    <td width="33%">
                        <strong>
                            <p>Language Spoken</p>
                        </strong>
                        <p>
                            {{ is_array($record->languages_spoken) ? implode(', ', $record->languages_spoken) :
                            ($record->languages_spoken ?? 'No Languages') }}
                        </p>
                    </td>
                    <td width="33%">
                        <strong>Hometown</strong>
                        <p>{{ $record->hometown }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>
                            <p>Country of Origin</p>
                        </strong>
                        <p>{{ $record->nationality }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>Marital Status</p>
                        </strong>
                        <p>{{ $record->married_status }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>Education Background</p>
                        </strong>
                        <p>{{ $record->education_level }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>
                            <p>Religious Background</p>
                        </strong>
                        <p>{{ $record->religion }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>Occupation</p>
                        </strong>
                        <p>{{ $record->occupation }}</p>
                    </td>
                    <td>
                        <strong>
                            <p>Name of Next of Kin</p>
                        </strong>
                        <p>{{ $record->next_of_kin_name }}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>
                            <p>Next of Kin Relationship</p>
                        </strong>
                        <p>Son</p>
                    </td>
                    <td>
                        <strong>
                            <p>Contact of Next of Kin</p>
                        </strong>
                        <p>{{ $record->next_of_kin_contact }}</p>
                    </td>
                </tr>
            </table>

            <!-- Distinctive Body Marks Section -->
            <br>
            <table width="100%" cellspacing="0" cellpadding="10">
                <tr>
                    <td colspan="3">
                        <h2>Distinctive Body Marks</h2>
                    </td>
                </tr>
                <tr>
                    <td width="33%">
                        <strong>
                            <p>Distinctive Marks</p>
                        </strong>
                        <p>
                            {{ is_array($record->distinctive_marks) ? implode(', ', $record->distinctive_marks) :
                            ($record->distinctive_marks ?? '') }}
                        </p>
                    </td>
                    <td width="33%">
                        <strong>
                            <p>Part of the Body</p>
                        </strong>
                        <p>{{ $record->part_of_the_body }}</p>
                    </td>
                </tr>
            </table>

            <!-- Previous Conviction Section -->
            <br>
            <table width="100%" cellspacing="0" cellpadding="10">
                <tr>
                    <td colspan="3">
                        <h2>Previous Conviction</h2>
                    </td>
                </tr>
                @if (!empty($record->previous_convictions) && is_array($record->previous_convictions))
                @foreach ($record->previous_convictions as $previous_conviction)
                <tr>
                    <td width="33%">
                        <strong>Previous Sentence</strong>
                        <p>{{ $previous_conviction['previous_sentence'] ?? '' }}</p>
                    </td>
                    <td width="33%">
                        <strong>Previous Offence</strong>
                        <p>
                            {{ $previous_conviction['previous_offence'] ?? '' }}
                        </p>

                    </td>
                    <td width="33%">
                        <strong>Station</strong>
                        <p>
                            {{ !empty($previous_conviction['previous_station_id']) ? \App\Models\Station::where('id',
                            $previous_conviction['previous_station_id'])->pluck('name')->first() : '' }}
                        </p>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">
                        <p>No previous conviction recorded.</p>
                    </td>
                </tr>
                @endif

            </table>

            <!-- Police Information Section -->
            <br>
            <table width="100%" cellspacing="0" cellpadding="10">
                <tr>
                    <td colspan="3">
                        <h2>Police Information</h2>
                    </td>
                </tr>
                <tr>
                    <td width="33%">
                        <strong>
                            <p>Police Name</p>
                        </strong>
                        <p>{{ $record->police_name }}</p>
                    </td>
                    <td width="33%">
                        <strong>
                            <p>Police Station</p>
                        </strong>
                        <p>{{ $record->police_station }}</p>
                    </td>
                    <td width="33%">
                        <strong>
                            <p>Police Contact</p>
                        </strong>
                        <p>{{ $record->police_contact }}</p>
                    </td>
                </tr>
            </table>

            <!-- Sentences Section -->
            <br>
            <table width="100%" cellspacing="0" cellpadding="10">
                <thead>
                    <tr>
                        <th><strong>Sentence</strong></th>
                        <th><strong>Offence</strong></th>
                        <th><strong>EPD</strong></th>
                        <th><strong>LPD</strong></th>
                        <th><strong>Court of Committal</strong></th>
                        <th><strong>Committed By</strong></th>
                        <th><strong>Committed Sentence</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>20yrs INL</td>
                        <td>Stealing</td>
                        <td>Sep 27, 2025</td>
                        <td>Sep 25, 2025</td>
                        <td>Accra High Court</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>