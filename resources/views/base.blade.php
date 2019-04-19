@extends('layouts.vueApp')

@section('header')


@endsection

@section('content')
<div style="">
  <router-view v-bind:mixconfig="mixconfig" v-bind:canloadmore="canloadmore"></router-view>
</div>
@endsection
