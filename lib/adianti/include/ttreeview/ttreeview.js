function ttreeview_start( id, collapsed )
{
    $( id ).treeview({
        persist: 'location',
        animated: 'fast',
        collapsed: collapsed
    });
}