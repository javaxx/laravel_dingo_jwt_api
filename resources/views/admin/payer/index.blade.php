@extends("admin.layout.main")

@section("content")
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-10 col-xs-6">
                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title">车票列表</h3>
                    </div>
                    <a type="button" class="btn " href="/admin/roles/create" >新增车票</a>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tbody><tr>
                                <th style="width: 10px">#</th>
                                <th>身份证号</th>
                                <th>绑定的微信名称</th>
                                <th>购买人</th>
                                <th>状态 </th>
                                <th>操作</th>
                            </tr>
                                @foreach($payers as $payer)
                                <tr>
                                    <td>{{$payer->id}}.</td>
                                    <td>{{$payer->idCard}}</td>
                                    <td> @foreach($payer->users as $user) {{$user->name}}  ,@endforeach</td>
                                    <td>
                                       {{$payer->status}}
{{--   @if ($payer->status  == 0)
                                            有效
                                        @elseif ($payer->status  == 1)
                                           无效,已验票
                                        @elseif ($payer->status == 2)
                                            无效,已退票
                                        @endif--}}
                                    </td>
                                    <td>
                                        {{--<a type="button" class="btn" href="/admin/tickets/{{$payer->id}}/update"  >权限管理</a>--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody></table>
                    </div>
                    {{$payers->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection