@extends("emails.layouts.layout")
@section("body")

    <style>
        .property-card {
            border: 1px solid #ddd;
            border-radius: 0.2rem;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .property-image {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }

        .property-body {
            padding: 1rem;
        }

        .property-price {
            margin-bottom: 0;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .property-info,
        .property-details {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .property-footer {
            border-top: 1px solid #eee;
            padding: 0.75rem 1rem;
        }

        .agent-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .agent-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .agent-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .agent-name {
            margin: 0;
            font-size: 1rem;
        }

        .agent-email {
            margin: 0;
            font-size: 0.875rem;
            color: #666;
        }

        .contact-links a {
            margin-left: 1rem;
            color: #007bff;
            text-decoration: none;
        }

        .contact-links a:hover {
            text-decoration: underline;
        }

    </style>

    <h4>Dear {{ $user->name ?? "" }},</h4>

    Here are matching properties for your client:
    <br><br>

    @foreach($properties ?? [] as $property)
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #ddd; border-radius:4px; margin-bottom: 10px">
            <tr>
                <td>
                    <a href="{{ route('front.auth.listing.details', $property->id) }}" target="_blank">
                        <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/with_watermark/copy_copy_copy_copy_copy_171956903120240628140351Yon0rwKXZh.jpg" alt="Property Image" style="width:100%; display:block;">
                    </a>
                </td>
            </tr>
            <tr>
                <td style="padding:16px;">
                    <h4 style="margin:0 0 10px; font-size:18px;">AED {{ formatNumberMetricPrefixes($property->price->price ?? 0) }}</h4>
                    <p style="margin:0 0 6px;">
                        <img src="{{ asset('images/mail/bed-outline.png') }}" alt="Bedrooms" width="15" height="15" />
                        {{ $property->bed->name ?? "" }}
                        ·
                        <img src="{{ asset('images/mail/shower.png') }}" alt="bathrooms" width="15" height="15" />
                        {{ $property->bath->name ?? "" }}
                        ·
                        <img src="{{ asset('images/mail/grid-large.png') }}" alt="Built Up Area" width="15" height="15" />
                        {{ $property->built_up_area ?? "" }} sqft
                    </p>
                    <p style="margin:0 0 6px;">
                        <a href="{{ route('front.auth.listing.details', $property->id) }}" style="text-decoration:none; color:#090909;" target="_blank">
                            {{ $property->title ?? "" }}
                        </a>
                    </p>
                    <p style="margin:0 0 6px;">{{ $property->location ?? "" }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding:16px; border-top:1px solid #eee;">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                            <td width="50" valign="top">
                                <img src="{{ $property?->agent?->thumbnail ?? '' }}" style="border-radius:50%; width:48px;" alt="">
                            </td>
                            <td valign="top" style="padding-left:10px;">
                                <p style="margin:0; font-weight:bold;">{{ $property?->agent?->name ?? "" }}</p>
                                <p style="margin:0;">{{ $property?->agent?->email ?? "" }}</p>
                            </td>
                            <td align="right">
                                @if( $property?->agent?->mobile ?? '')
                                    <a href="tel:{{ $property?->agent?->mobile ?? '' }}" style="border-radius:1px; text-decoration:none; color:#007bff;">
                                        <img src="{{ asset('images/mail/phone.png') }}" alt="Built Up Area" width="18" height="18" />
                                    </a>
                                @endif
                                &nbsp;
                                &nbsp;
                                @if( $property?->agent?->telegram_username ?? '')
                                    <a href="https://t.me/{{ $property?->agent?->telegram_username ?? '' }}">
                                        <a href="tel:{{ $property?->agent?->mobile ?? '' }}" style="border-radius:1px; text-decoration:none; color:#007bff;">
                                            <img src="{{ asset('images/mail/send_rotated_-45.png') }}" alt="Built Up Area" width="18" height="18" style="rotate: -45deg" />
                                        </a>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @endforeach
    <br><br>
    Best regards,
@endsection
