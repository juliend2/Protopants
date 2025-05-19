<?php

Prototype::create('Object',
    properties: [
    ],
    methods: [
        'toString' => function ($self) {
            return "[{$self->__prototypeName}]";
        }
    ]
);
