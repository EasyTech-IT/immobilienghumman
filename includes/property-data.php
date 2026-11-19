<?php

function property_public_extra_data(array $extra): array
{
    unset($extra['address_public_note'], $extra['legacy_slugs'], $extra['documents'], $extra['private_expose']);
    $extra['hide_exact_address'] = true;
    return $extra;
}
