@extends('mail.layout')

@section('mail-body')
    <div class="es-wrapper-color">
        <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="esd-email-paddings" valign="top">
                    <table class="es-content esd-footer-popover" cellspacing="0" cellpadding="0" align="center">
                        <tbody>
                        <tr>
                            <td class="esd-stripe" align="center">
                                <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center">
                                    <tbody>
                                    <tr>
                                        <td class="es-p25t es-p20r es-p20l esd-structure" align="left">
                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                <tbody>
                                                <tr>
                                                    <td class="esd-container-frame" width="560" valign="top" align="center">
                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                            <tbody>
                                                            <tr>
                                                                <td align="center" class="esd-block-image" style="font-size: 0px;"><a target="_blank"><img class="adapt-img" src="{{ asset(getSettingValue('logo')) }}" alt style="display: block;" height="55"></a></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="esd-structure es-p20t es-p20r es-p20l" align="left">
                                            <table cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                    <td width="560" class="esd-container-frame" align="center" valign="top">
                                                        <table cellpadding="0" cellspacing="0" width="100%">
                                                            <tbody>
                                                            <tr>
                                                                <td align="center" class="esd-block-text">
                                                                    <span style="font-size: 24px; color: #666666;">Ticket Successfully Created</span>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="esd-structure es-p20t es-p20r es-p20l" align="left">
                                            <table cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                    <td width="560" class="esd-container-frame" align="center" valign="top">
                                                        <table cellpadding="0" cellspacing="0" width="100%">
                                                            <tbody>
                                                            <tr>
                                                                <td align="left" class="esd-block-text">
                                                                    <p style="color: #666666;">Hello!&nbsp;{{ $user_name }},<br><br>This message is to inform that customer&nbsp;created a ticket<strong><span>&nbsp;</span></strong>and the details are as follows:<br><br>Customer Name:&nbsp;<strong>{{ \App\Models\User::whereId($created_by)->firstOrFail()->name }}</strong><br>Ticket Title:&nbsp;<strong>{{ $title }}</strong><br>Ticket ID:&nbsp;<strong>{{ $ticket_id }}</strong><br><br>For more information visit the ticket detail screen.<br><br>Thank you.</p>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="esd-structure es-p20" align="left">
                                            <table cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                    <td width="560" class="esd-container-frame" align="center" valign="top">
                                                        <table cellpadding="0" cellspacing="0" width="100%">
                                                            <tbody>
                                                            <tr>
                                                                <td align="center" class="esd-block-button"><span class="es-button-border" style="border-radius: 5px; border-width: 0px; border-color: #6b719b; background: #6b719b;"><a href="{{ route('web.search_ticket', ['ticket_id' => $ticket_id, 'email' => $email]) }}" class="es-button es-button-1634101519969" target="_blank" style="border-radius: 5px; border-width: 10px 15px; background: #6b719b; border-color: #6b719b;">View Ticket</a></span></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
