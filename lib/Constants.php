<?php

namespace Sabre\BObject;

class Constants {

    const FORMAT_VERSION = 0x01;

    const TYPE_BYTE     = 0x01;
    const TYPE_INTEGER  = 0x02;
    const TYPE_STRING   = 0x03;
    const TYPE_ARRAY    = 0x04; 
    const TYPE_OBJECT   = 0x05;
    const TYPE_HALFBYTE = 0x06;
    const TYPE_WORD     = 0x07;

    static $dictionary = [

        // Components
        'VCALENDAR',
        'VTIMEZONE',
        'VEVENT',
        'DAYLIGHT',
        'STANDARD',

        // Properties
        'METHOD',
        'VERSION',
        'PRODID',
        'CALSCALE',
        'TZID',
        'TZOFFSETFROM',
        'TZOFFSETTO',
        'RRULE',
        'TZNAME',
        'DTSTART',
        'CREATED',
        'UID',
        'DTEND',
        'TRANSP',
        'SUMMARY',
        'DTSTAMP',
        'SEQUENCE',

        // Types
        'TEXT',
        'UNKNOWN',
        'UTC-OFFSET',
        'RECUR',
        'DATE-TIME',
        'INTEGER',

        // Values
        'PUBLISH',
        'GREGORIAN',
        'YEARLY',
        '2.0',
        'OPAQUE',

        // TZ
        'America/Los_Angeles',
        'America/New_York',
    ];

}
