<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td>
                                    <a href="{{ $url }}" class="button button-{{ $color ?? 'primary' }}" style="background-color: #d3a188;border-color: #d3a188; color: #000; text-align: center;" target="_blank"
                                       rel="noopener">{{ $slot }}</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
