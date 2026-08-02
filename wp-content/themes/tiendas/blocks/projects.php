<div id="projects" class="container-fluid px-0">

    <div class="row">

        <div class="col-12">
            <div class="container px-0">
                <div class="row">
                    <div data-aos="fade-up" class="col-12 d-flex align-items-center justify-content-center text-center">
                        <h2><?php echo get_field('title')?></h2>
                    </div>
                </div>
            </div>
        </div>

	</div>

    <?php $projects = get_posts(array('post_type' => 'project', 'post_status' => 'publish', 'posts_per_page' => -1));?>
    <?php if(is_array($projects) and count($projects) > 0):?>
        <div class="row projects" data-aos="fade-up">
            <?php foreach($projects as $project):?>
                <div class="col project" style="background: <?php echo get_field('background_color', $project->ID)?>;">
                    <div class="project-content">
                        <div class="description mb-md-5 mb-sm-3 mb-3" data-aos="fade-up"><?php echo apply_filters('the_content', $project->post_content)?></div>
                        <?php if(get_field('project_link', $project->ID)):?>
                            <div class="d-flex align-items-center justify-content-center">
                                <a class="btn btn-secondary mt-2 mb-5" data-aos="fade-up" href="<?php echo get_field('project_link', $project->ID)['url']?>" target="<?php echo get_field('project_link', $project->ID)['target']?>" title="<?php echo htmlentities(get_field('project_link', $project->ID)['title'])?>">
                                    <?php echo (get_field('project_link', $project->ID)['title'])?>
                                </a>
                            </div>
                        <?php endif;?>
                        <img class="img-fluid" data-aos="fade-up" src="<?php echo get_the_post_thumbnail_url($project->ID, 'full')?>" alt="<?php echo get_the_title($project->ID)?>" />
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    <?php endif;?>

</div>